// @ts-nocheck
import { Pool } from "https://deno.land/x/postgres@v0.17.2/mod.ts";
import { createClient } from "https://esm.sh/@supabase/supabase-js@2";
import { PDFDocument, StandardFonts, rgb } from "https://esm.sh/pdf-lib@1.17.1";

type Devis = Record<string, unknown>;

// Base64 du PDF maquette (laisser vide si non utilisé)
const EMBEDDED_TEMPLATE_B64 = "";

function jsonResponse(body: unknown, init: ResponseInit = {}): Response {
  const headers = new Headers(init.headers);
  if (!headers.has("Content-Type"))
    headers.set("Content-Type", "application/json; charset=utf-8");
  headers.set("Access-Control-Allow-Origin", "*");
  headers.set("Access-Control-Allow-Methods", "GET,POST,OPTIONS");
  headers.set("Access-Control-Allow-Headers", "authorization, content-type");
  const serialized = JSON.stringify(body, (_key, value) =>
    typeof value === "bigint" ? value.toString() : value
  );
  return new Response(serialized, { ...init, headers });
}

function badRequest(message: string, status = 400): Response {
  return jsonResponse({ error: message }, { status });
}

function serverError(message: string): Response {
  return jsonResponse({ error: message }, { status: 500 });
}

function getIdFromPath(url: URL, functionSlug: string): string | null {
  const segments = url.pathname.split("/").filter(Boolean);
  const idx = segments.findIndex((s) => s === functionSlug);
  if (idx === -1) return null;
  return segments[idx + 1] ?? null;
}

let pool: Pool | null = null;

async function ensurePool(): Promise<Pool> {
  if (pool) return pool;
  const databaseUrl = Deno.env.get("SUPABASE_DB_URL");
  if (!databaseUrl) throw new Error("Missing SUPABASE_DB_URL env variable");
  pool = new Pool(databaseUrl, 3, true);
  return pool;
}

async function fetchDevisById(id: number): Promise<Devis | null> {
  const p = await ensurePool();
  const conn = await p.connect();
  try {
    const result = await conn.queryObject<Devis>({
      text: 'select * from "laravel"."devis" where id = $1',
      args: [id],
    });
    return result.rows[0] ?? null;
  } finally {
    conn.release();
  }
}

function renderText(value: unknown): string {
  if (value === null || value === undefined) return "";
  if (typeof value === "bigint") return value.toString();
  return String(value);
}

function base64ToUint8Array(b64: string): Uint8Array {
  const binary = atob(b64);
  const len = binary.length;
  const bytes = new Uint8Array(len);
  for (let i = 0; i < len; i++) bytes[i] = binary.charCodeAt(i);
  return bytes;
}

async function maybeLoadTemplateBytesFromStorage(bucket: string, path: string): Promise<Uint8Array | null> {
  const url = Deno.env.get("SUPABASE_URL");
  const serviceKey = Deno.env.get("SUPABASE_SERVICE_ROLE_KEY");
  if (!url || !serviceKey) return null;
  const client = createClient(url, serviceKey, { auth: { persistSession: false } });
  const { data, error } = await client.storage.from(bucket).download(path);
  if (error || !data) return null;
  const arrayBuffer = await data.arrayBuffer();
  return new Uint8Array(arrayBuffer);
}

async function generatePdfFromDevis(devis: Devis, templateBytes?: Uint8Array | null): Promise<Uint8Array> {
  let pdfDoc: PDFDocument;
  let page;
  if (templateBytes && templateBytes.length > 0) {
    const templateDoc = await PDFDocument.load(templateBytes);
    pdfDoc = await PDFDocument.create();
    const [tpl] = await pdfDoc.copyPages(templateDoc, [0]);
    page = pdfDoc.addPage(tpl);
  } else {
    pdfDoc = await PDFDocument.create();
    page = pdfDoc.addPage([595.28, 841.89]);
  }
  const font = await pdfDoc.embedFont(StandardFonts.Helvetica);
  const { width, height } = page.getSize();

  const title = `Devis ${renderText(devis["numero_devis"])}`;
  page.drawText(title, {
    x: 50,
    y: height - 70,
    size: 22,
    font,
    color: rgb(0, 0, 0),
  });

  const lines: string[] = [];
  lines.push(`Client ID: ${renderText(devis["client_id"])}`);
  lines.push(`Date du devis: ${renderText(devis["date_devis"])}`);
  lines.push(`Objet: ${renderText(devis["objet"])}`);
  lines.push(`Description: ${renderText(devis["description"])}`);
  lines.push(`Montant HT: ${renderText(devis["montant_ht"])}`);
  lines.push(`TVA (%): ${renderText(devis["taux_tva"])}`);
  lines.push(`Montant TVA: ${renderText(devis["montant_tva"])}`);
  lines.push(`Montant TTC: ${renderText(devis["montant_ttc"])}`);
  lines.push(`Conditions: ${renderText(devis["conditions"])}`);
  lines.push(`Notes: ${renderText(devis["notes"])}`);

  let y = height - 110;
  for (const line of lines) {
    page.drawText(line, { x: 50, y, size: 12, font });
    y -= 18;
  }

  return await pdfDoc.save();
}

async function uploadToStorage(
  bytes: Uint8Array,
  bucket: string,
  path: string,
  opts: { public: boolean; signedTTLSeconds?: number }
) {
  const url = Deno.env.get("SUPABASE_URL");
  const serviceKey = Deno.env.get("SUPABASE_SERVICE_ROLE_KEY");
  if (!url || !serviceKey)
    throw new Error(
      "Missing SUPABASE_URL or SUPABASE_SERVICE_ROLE_KEY env variable"
    );

  const client = createClient(url, serviceKey, {
    auth: { persistSession: false },
  });

  // Ensure bucket exists
  const { error: createErr } = await client.storage.createBucket(bucket, {
    public: opts.public,
    fileSizeLimit: 50 * 1024 * 1024,
  });
  if (
    createErr &&
    !String(createErr.message).toLowerCase().includes("already exists")
  ) {
    // Non-fatal: if exists, ignore
    // But if another error, throw
    throw createErr;
  }

  // S'assurer que le bucket a la bonne visibilité (public/privé)
  await client.storage.updateBucket(bucket, { public: opts.public }).catch(() => {});

  const { error: uploadErr } = await client.storage
    .from(bucket)
    .upload(path, new Blob([bytes], { type: "application/pdf" }), {
      upsert: true,
      contentType: "application/pdf",
    });
  if (uploadErr) throw uploadErr;

  if (opts.public) {
    const { data: pub } = client.storage.from(bucket).getPublicUrl(path);
    return { path, publicUrl: pub?.publicUrl ?? null, signedUrl: null };
  } else {
    const expiresIn = Number.isFinite(Number(opts.signedTTLSeconds)) && Number(opts.signedTTLSeconds) > 0
      ? Number(opts.signedTTLSeconds)
      : 60 * 60 * 24 * 365; // 1 an par défaut
    const { data: signed, error: signedErr } = await client.storage
      .from(bucket)
      .createSignedUrl(path, expiresIn);
    if (signedErr) throw signedErr;
    return { path, publicUrl: null, signedUrl: signed?.signedUrl ?? null };
  }
}

async function updateDevisFileInfo(
  id: number,
  filePath: string,
  publicUrl: string | null
) {
  const p = await ensurePool();
  const conn = await p.connect();
  try {
    await conn.queryObject({
      text: 'update "laravel"."devis" set pdf_file = $1, pdf_url = $2, updated_at = now() where id = $3',
      args: [filePath, publicUrl, id],
    });
  } finally {
    conn.release();
  }
}

Deno.serve(async (req) => {
  if (req.method === "OPTIONS") return jsonResponse({ ok: true });

  try {
    const url = new URL(req.url);
    const idStr = getIdFromPath(url, "generate-devis-pdf");
    if (!idStr)
      return badRequest(
        "ID du devis manquant dans l'URL. Utilisez /generate-devis-pdf/{id}"
      );
    const id = Number(idStr);
    if (!Number.isFinite(id)) return badRequest("ID invalide");

    const bucket = url.searchParams.get("bucket") ?? "devis-pdfs";
    const path = url.searchParams.get("path") ?? `devis/${id}.pdf`;
    const isPublic = (url.searchParams.get("public") ?? "false").toLowerCase() === "true";
    const signedTTLSeconds = Number(url.searchParams.get("signed_ttl") ?? "31536000");

    // Chargement de la maquette: priorité à l'embedded, sinon Storage via params, sinon aucune
    let templateBytes: Uint8Array | null = null;
    if (EMBEDDED_TEMPLATE_B64 && EMBEDDED_TEMPLATE_B64.length > 0) {
      templateBytes = base64ToUint8Array(EMBEDDED_TEMPLATE_B64);
    } else {
      const tBucket = url.searchParams.get("template_bucket");
      const tPath = url.searchParams.get("template_path");
      if (tBucket && tPath) {
        templateBytes = await maybeLoadTemplateBytesFromStorage(tBucket, tPath);
      }
    }

    const devis = await fetchDevisById(id);
    if (!devis) return badRequest("Devis introuvable", 404);

    const pdfBytes = await generatePdfFromDevis(devis, templateBytes);
    const { path: storagePath, publicUrl, signedUrl } = await uploadToStorage(
      pdfBytes,
      bucket,
      path,
      { public: isPublic, signedTTLSeconds }
    );
    const finalUrl = isPublic ? publicUrl : signedUrl;
    await updateDevisFileInfo(id, storagePath, finalUrl ?? null);

    return jsonResponse({ ok: true, id, storagePath, publicUrl, signedUrl });
  } catch (e) {
    const message = e instanceof Error ? e.message : String(e);
    return serverError(message);
  }
});
