// @ts-nocheck
import { Pool } from "https://deno.land/x/postgres@v0.17.2/mod.ts";
import { createClient } from "https://esm.sh/@supabase/supabase-js@2";
import { PDFDocument, StandardFonts, rgb } from "https://esm.sh/pdf-lib@1.17.1";

type Devis = Record<string, unknown>;
type Client = Record<string, unknown>;
type Entreprise = Record<string, unknown>;
type LigneDevis = {
  service_nom?: string | null;
  quantite?: number | null;
  unite?: string | null;
  prix_unitaire_ht?: number | null;
  remise_pourcentage?: number | null;
  taux_tva?: number | null;
  description_personnalisee?: string | null;
  montant_ht?: number | null;
};
type Madinia = Record<string, unknown>;

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

async function fetchMadinia(): Promise<Madinia | null> {
  const p = await ensurePool();
  const conn = await p.connect();
  try {
    const result = await conn.queryObject<Madinia>({
      text: 'select * from "laravel"."madinia" limit 1',
    });
    return result.rows[0] ?? null;
  } finally {
    conn.release();
  }
}

async function fetchClientWithEntreprise(
  clientId: number
): Promise<{ client: Client | null; entreprise: Entreprise | null }> {
  const p = await ensurePool();
  const conn = await p.connect();
  try {
    const clientRes = await conn.queryObject<Client>({
      text: 'select * from "laravel"."clients" where id = $1',
      args: [clientId],
    });
    const client = clientRes.rows[0] ?? null;
    let entreprise: Entreprise | null = null;
    const entrepriseId = (client as any)?.entreprise_id ?? null;
    if (entrepriseId) {
      const entRes = await conn.queryObject<Entreprise>({
        text: 'select * from "laravel"."entreprises" where id = $1',
        args: [entrepriseId],
      });
      entreprise = entRes.rows[0] ?? null;
    }
    return { client, entreprise };
  } finally {
    conn.release();
  }
}

async function fetchLignesForDevis(devisId: number): Promise<LigneDevis[]> {
  const p = await ensurePool();
  const conn = await p.connect();
  try {
    const result = await conn.queryObject<LigneDevis>({
      text:
        "select ld.quantite, ld.unite, ld.prix_unitaire_ht, ld.remise_pourcentage, ld.taux_tva, ld.description_personnalisee, ld.montant_ht, s.nom as service_nom " +
        'from "laravel"."lignes_devis" ld ' +
        'left join "laravel"."services" s on s.id = ld.service_id ' +
        "where ld.devis_id = $1 order by ld.ordre asc nulls last, ld.id asc",
      args: [devisId],
    });
    return result.rows;
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

async function maybeLoadTemplateBytesFromStorage(
  bucket: string,
  path: string
): Promise<Uint8Array | null> {
  const url = Deno.env.get("SUPABASE_URL");
  const serviceKey = Deno.env.get("SUPABASE_SERVICE_ROLE_KEY");
  if (!url || !serviceKey) return null;
  const client = createClient(url, serviceKey, {
    auth: { persistSession: false },
  });
  const { data, error } = await client.storage.from(bucket).download(path);
  if (error || !data) return null;
  const arrayBuffer = await data.arrayBuffer();
  return new Uint8Array(arrayBuffer);
}

async function generatePdfFromDevis(
  devis: Devis & {
    lignes?: LigneDevis[];
    client?: Client & { entreprise?: Entreprise | null };
  } & { madinia?: Madinia | null },
  templateBytes?: Uint8Array | null
): Promise<Uint8Array> {
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
  let { width, height } = page.getSize();
  const marginX = 28;
  const contentX = marginX;
  const contentWidth = width - marginX * 2;

  const euro = (v: unknown) => {
    const num = Number(v ?? 0);
    const fixed = num.toFixed(2);
    const parts = fixed.split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    return parts.join(",") + " €";
  };
  const fmtDate = (d: unknown) => {
    if (!d) return "-";
    const dt = new Date(String(d));
    const dd = String(dt.getDate()).padStart(2, "0");
    const mm = String(dt.getMonth() + 1).padStart(2, "0");
    const yyyy = dt.getFullYear();
    return `${dd}/${mm}/${yyyy}`;
  };

  // Header
  const brand = String(
    (devis as any)?.madinia?.name ?? "Madin.IA"
  ).toUpperCase();
  const title = `Devis ${renderText(devis["numero_devis"])}`;
  const headerTop = height - 36;
  page.drawText(brand, {
    x: contentX,
    y: headerTop,
    size: 18,
    font,
    color: rgb(0.06, 0.09, 0.16),
  });
  page.drawText(title, {
    x: contentX + contentWidth - 220,
    y: headerTop,
    size: 14,
    font,
    color: rgb(0.06, 0.09, 0.16),
  });
  page.drawText(`Date: ${fmtDate((devis as any).date_devis)}`, {
    x: contentX + contentWidth - 220,
    y: headerTop - 16,
    size: 10,
    font,
    color: rgb(0.28, 0.34, 0.42),
  });
  page.drawRectangle({
    x: 0,
    y: headerTop - 26,
    width,
    height: 1,
    color: rgb(0.9, 0.91, 0.93),
  });

  // Identity blocks (first page)
  let cursorY = headerTop - 46;
  const leftX = contentX;
  const rightX = contentX + contentWidth / 2 + 8;
  page.drawText("Émetteur", {
    x: leftX,
    y: cursorY,
    size: 12,
    font,
    color: rgb(0.07, 0.07, 0.07),
  });
  page.drawRectangle({
    x: leftX,
    y: cursorY - 12,
    width: contentWidth / 2 - 12,
    height: 1,
    color: rgb(0.9, 0.91, 0.93),
  });
  let yL = cursorY - 24;
  const emitterLines: string[] = [];
  emitterLines.push(
    `Entreprise: ${renderText((devis as any)?.madinia?.name ?? "Madin.IA")}`
  );
  emitterLines.push(
    `Contact: ${renderText((devis as any)?.user?.name ?? "Administration")}`
  );
  emitterLines.push(
    `Email: ${renderText(
      (devis as any)?.user?.email ??
        (devis as any)?.madinia?.email ??
        "contact@madinia.fr"
    )}`
  );
  if ((devis as any)?.madinia?.telephone)
    emitterLines.push(
      `Téléphone: ${renderText((devis as any)?.madinia?.telephone)}`
    );
  if ((devis as any)?.madinia?.adresse)
    emitterLines.push(
      `Adresse: ${renderText((devis as any)?.madinia?.adresse)}`
    );
  if ((devis as any)?.madinia?.siret)
    emitterLines.push(`SIRET: ${renderText((devis as any)?.madinia?.siret)}`);
  for (const l of emitterLines) {
    page.drawText(l, {
      x: leftX,
      y: yL,
      size: 11,
      font,
      color: rgb(0.06, 0.09, 0.16),
    });
    yL -= 16;
  }

  page.drawText("Client", {
    x: rightX,
    y: cursorY,
    size: 12,
    font,
    color: rgb(0.07, 0.07, 0.07),
  });
  page.drawRectangle({
    x: rightX,
    y: cursorY - 12,
    width: contentWidth / 2 - 12,
    height: 1,
    color: rgb(0.9, 0.91, 0.93),
  });
  let yR = cursorY - 24;
  const client = (devis as any)?.client ?? {};
  const entreprise = (client as any)?.entreprise ?? null;
  const clientLines: string[] = [];
  clientLines.push(
    `Nom: ${renderText(
      (client as any)?.nom ?? (client as any)?.raison_sociale
    )}`
  );
  if (entreprise?.nom)
    clientLines.push(`Entreprise: ${renderText(entreprise?.nom)}`);
  if (client?.adresse)
    clientLines.push(`Adresse: ${renderText(client?.adresse)}`);
  if ((client as any)?.ville || (client as any)?.code_postal)
    clientLines.push(
      `Ville: ${renderText((client as any)?.code_postal)} ${renderText(
        (client as any)?.ville
      )}`
    );
  for (const l of clientLines) {
    page.drawText(l, {
      x: rightX,
      y: yR,
      size: 11,
      font,
      color: rgb(0.06, 0.09, 0.16),
    });
    yR -= 16;
  }

  cursorY = Math.min(yL, yR) - 8;

  // Table header
  const colServiceW = contentWidth * 0.4;
  const colQtyW = contentWidth * 0.12;
  const colPriceW = contentWidth * 0.16;
  const colDiscountW = contentWidth * 0.12;
  const colVatW = contentWidth * 0.08;
  const colTotalW = contentWidth * 0.12;
  const rowH = 18;
  const headerY = cursorY;
  page.drawRectangle({
    x: contentX,
    y: headerY - 12,
    width: contentWidth,
    height: 24,
    color: rgb(0.95, 0.96, 0.97),
  });
  page.drawText("Service / Description", {
    x: contentX + 6,
    y: headerY - 6,
    size: 10,
    font,
    color: rgb(0.07, 0.07, 0.07),
  });
  page.drawText("Qté", {
    x: contentX + colServiceW + 6,
    y: headerY - 6,
    size: 10,
    font,
    color: rgb(0.07, 0.07, 0.07),
  });
  page.drawText("Prix HT", {
    x: contentX + colServiceW + colQtyW + colPriceW - 44,
    y: headerY - 6,
    size: 10,
    font,
    color: rgb(0.07, 0.07, 0.07),
  });
  page.drawText("Remise", {
    x: contentX + colServiceW + colQtyW + colPriceW + 6,
    y: headerY - 6,
    size: 10,
    font,
    color: rgb(0.07, 0.07, 0.07),
  });
  page.drawText("TVA", {
    x: contentX + colServiceW + colQtyW + colPriceW + colDiscountW + 6,
    y: headerY - 6,
    size: 10,
    font,
    color: rgb(0.07, 0.07, 0.07),
  });
  page.drawText("Total HT", {
    x: contentX + contentWidth - 54,
    y: headerY - 6,
    size: 10,
    font,
    color: rgb(0.07, 0.07, 0.07),
  });

  const lignes: LigneDevis[] = Array.isArray((devis as any)?.lignes)
    ? ((devis as any)?.lignes as LigneDevis[])
    : [];
  const LINES_PER_PAGE = 8;
  let rowIndex = 0;
  let y = headerY - 28;
  const drawRow = (ld: LigneDevis) => {
    const serviceName = String(ld.service_nom ?? "Service");
    page.drawText(serviceName, {
      x: contentX + 6,
      y,
      size: 10,
      font,
      color: rgb(0.06, 0.09, 0.16),
    });
    if (ld.description_personnalisee) {
      page.drawText(String(ld.description_personnalisee), {
        x: contentX + 6,
        y: y - 12,
        size: 9,
        font,
        color: rgb(0.42, 0.45, 0.5),
        maxWidth: colServiceW - 12,
      });
    }
    const qty = Number(ld.quantite ?? 0);
    const unit = String(ld.unite ?? "").toLowerCase();
    let unitLabel = unit || "";
    if (unitLabel === "unite") unitLabel = "unité";
    const qtyLabel = `${qty} ${
      qty > 1 && unitLabel && !["mois", "forfait"].includes(unitLabel)
        ? unitLabel + "s"
        : unitLabel
    }`.trim();
    page.drawText(qtyLabel, {
      x: contentX + colServiceW + 6,
      y,
      size: 10,
      font,
      color: rgb(0.28, 0.34, 0.42),
    });
    page.drawText(euro(ld.prix_unitaire_ht), {
      x: contentX + colServiceW + colQtyW + colPriceW - 6 - 48,
      y,
      size: 10,
      font,
      color: rgb(0.06, 0.09, 0.16),
    });
    const remise = Number(ld.remise_pourcentage ?? 0);
    page.drawText(`${remise.toFixed(0)}%`, {
      x: contentX + colServiceW + colQtyW + colPriceW + 6,
      y,
      size: 10,
      font,
      color: rgb(0.28, 0.34, 0.42),
    });
    const tva = Number(ld.taux_tva ?? 0);
    page.drawText(`${tva.toFixed(0)}%`, {
      x: contentX + colServiceW + colQtyW + colPriceW + colDiscountW + 6,
      y,
      size: 10,
      font,
      color: rgb(0.28, 0.34, 0.42),
    });
    page.drawText(euro(ld.montant_ht), {
      x: contentX + contentWidth - 6 - 54,
      y,
      size: 10,
      font,
      color: rgb(0.06, 0.09, 0.16),
    });
  };
  for (const ld of lignes) {
    drawRow(ld);
    y -= rowH + (ld.description_personnalisee ? 8 : 0);
    rowIndex++;
    if (rowIndex % LINES_PER_PAGE === 0 && rowIndex < lignes.length) {
      page = pdfDoc.addPage([595.28, 841.89]);
      ({ width, height } = page.getSize());
      page.drawText(title, {
        x: contentX,
        y: height - 36,
        size: 12,
        font,
        color: rgb(0.06, 0.09, 0.16),
      });
      page.drawRectangle({
        x: 0,
        y: height - 48,
        width,
        height: 1,
        color: rgb(0.9, 0.91, 0.93),
      });
      y = height - 66;
      page.drawRectangle({
        x: contentX,
        y: y - 12,
        width: contentWidth,
        height: 24,
        color: rgb(0.95, 0.96, 0.97),
      });
      page.drawText("Service / Description", {
        x: contentX + 6,
        y: y - 6,
        size: 10,
        font,
        color: rgb(0.07, 0.07, 0.07),
      });
      page.drawText("Qté", {
        x: contentX + colServiceW + 6,
        y: y - 6,
        size: 10,
        font,
        color: rgb(0.07, 0.07, 0.07),
      });
      page.drawText("Prix HT", {
        x: contentX + colServiceW + colQtyW + colPriceW - 44,
        y: y - 6,
        size: 10,
        font,
        color: rgb(0.07, 0.07, 0.07),
      });
      page.drawText("Remise", {
        x: contentX + colServiceW + colQtyW + colPriceW + 6,
        y: y - 6,
        size: 10,
        font,
        color: rgb(0.07, 0.07, 0.07),
      });
      page.drawText("TVA", {
        x: contentX + colServiceW + colQtyW + colPriceW + colDiscountW + 6,
        y: y - 6,
        size: 10,
        font,
        color: rgb(0.07, 0.07, 0.07),
      });
      page.drawText("Total HT", {
        x: contentX + contentWidth - 54,
        y: y - 6,
        size: 10,
        font,
        color: rgb(0.07, 0.07, 0.07),
      });
      y -= 28;
    }
  }

  // Totals block
  const totalsX = contentX + contentWidth / 2;
  const totalsY = Math.max(90, y - 8);
  page.drawRectangle({
    x: totalsX,
    y: totalsY,
    width: contentWidth / 2 - 2,
    height: 70,
    color: rgb(0.98, 0.98, 0.98),
  });
  page.drawRectangle({
    x: totalsX,
    y: totalsY,
    width: contentWidth / 2 - 2,
    height: 70,
    borderColor: rgb(0.9, 0.91, 0.93),
    borderWidth: 1,
  });
  page.drawText("Sous-total HT", {
    x: totalsX + 10,
    y: totalsY + 48,
    size: 11,
    font,
  });
  page.drawText(euro((devis as any).montant_ht), {
    x: totalsX + contentWidth / 2 - 90,
    y: totalsY + 48,
    size: 11,
    font,
  });
  page.drawText(`TVA (${renderText((devis as any).taux_tva)}%)`, {
    x: totalsX + 10,
    y: totalsY + 30,
    size: 11,
    font,
    color: rgb(0.28, 0.34, 0.42),
  });
  page.drawText(euro((devis as any).montant_tva), {
    x: totalsX + contentWidth / 2 - 90,
    y: totalsY + 30,
    size: 11,
    font,
  });
  page.drawRectangle({
    x: totalsX + 10,
    y: totalsY + 22,
    width: contentWidth / 2 - 22,
    height: 1,
    color: rgb(0.9, 0.91, 0.93),
  });
  page.drawText("Total TTC", {
    x: totalsX + 10,
    y: totalsY + 6,
    size: 12,
    font,
  });
  page.drawText(euro((devis as any).montant_ttc), {
    x: totalsX + contentWidth / 2 - 90,
    y: totalsY + 6,
    size: 12,
    font,
  });

  // Footer
  const footerText = `${renderText(
    (devis as any)?.madinia?.name ?? "Madin.IA"
  )} — ${renderText(
    (devis as any)?.madinia?.adresse ?? "1 Chemin du Sud, 97233 Schoelcher"
  )} • SIRET ${renderText(
    (devis as any)?.madinia?.siret ?? "934 303 843 00015"
  )} • ${renderText((devis as any)?.madinia?.email ?? "contact@madinia.fr")}`;
  page.drawRectangle({
    x: 0,
    y: 40,
    width,
    height: 1,
    color: rgb(0.9, 0.91, 0.93),
  });
  page.drawText(footerText, {
    x: contentX,
    y: 26,
    size: 9,
    font,
    color: rgb(0.42, 0.45, 0.5),
    maxWidth: contentWidth,
  });

  return await pdfDoc.save();
}

function escapeHtml(input: string): string {
  return input
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/\"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function buildDevisHtml(doc: any): string {
  const euro = (v: unknown) => {
    const num = Number(v ?? 0);
    const fixed = num.toFixed(2);
    const parts = fixed.split(".") as string[];
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    return parts.join(",") + " €";
  };
  const fmtDate = (d: unknown) => {
    if (!d) return "-";
    const dt = new Date(String(d));
    const dd = String(dt.getDate()).padStart(2, "0");
    const mm = String(dt.getMonth() + 1).padStart(2, "0");
    const yyyy = dt.getFullYear();
    return `${dd}/${mm}/${yyyy}`;
  };
  const lignes = Array.isArray(doc.lignes) ? doc.lignes : [];

  const lignesRows = lignes
    .map(
      (l: any) => `
            <tr class="border-b border-slate-100 last:border-0">
              <td class="py-2 pr-3 align-top">
                <div class="font-semibold">${escapeHtml(
                  String(l.service_nom ?? "")
                )}</div>
                ${
                  l.description_personnalisee
                    ? `<div class="text-xs text-slate-500">${escapeHtml(
                        String(l.description_personnalisee)
                      )}</div>`
                    : ""
                }
              </td>
              <td class="py-2 text-center text-slate-600">${escapeHtml(
                String(l.quantite ?? "")
              )}</td>
              <td class="py-2 text-right">${escapeHtml(
                euro(l.prix_unitaire_ht)
              )}</td>
              <td class="py-2 text-right text-slate-600">${escapeHtml(
                String(Number(l.remise_pourcentage ?? 0).toFixed(0))
              )}%</td>
              <td class="py-2 text-right text-slate-600">${escapeHtml(
                String(Number(l.taux_tva ?? 0).toFixed(0))
              )}%</td>
              <td class="py-2 pl-3 text-right font-semibold">${escapeHtml(
                euro(l.montant_ht)
              )}</td>
            </tr>`
    )
    .join("");

  return `<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <style>
    @page { size: A4; margin: 18mm 14mm; }
    html, body { background: white; }
  </style>
  <script>
    // Config Tailwind si besoin
    tailwind.config = { theme: { extend: { colors: { brand: { 700: '#0F172A' } } } } };
  </script>
  <title>Devis ${escapeHtml(String(doc.numero_devis ?? ""))}</title>
</head>
<body>
  <main class="text-[11px] text-slate-800">
    <header class="pb-4 mb-4 border-b border-slate-200">
      <div class="flex items-end justify-between">
        <div class="text-[18px] font-bold tracking-wide">${escapeHtml(
          String(doc.madinia?.name ?? "Madin.IA")
        ).toUpperCase()}</div>
        <div class="text-right">
          <div class="text-[14px] font-bold">Devis ${escapeHtml(
            String(doc.numero_devis ?? "")
          )}</div>
          <div class="text-[10px] text-slate-500">Date: ${escapeHtml(
            fmtDate(doc.date_devis)
          )}</div>
        </div>
      </div>
    </header>

    <section class="grid grid-cols-2 gap-6 mb-4">
      <div>
        <div class="text-[12px] font-bold text-slate-900 uppercase pb-1 border-b border-slate-200">Émetteur</div>
        <div class="mt-3 space-y-1">
          <div><span class="font-semibold">Entreprise</span>: ${escapeHtml(
            String(doc.madinia?.name ?? "Madin.IA")
          )}</div>
          <div><span class="font-semibold">Contact</span>: ${escapeHtml(
            String(doc.user?.name ?? "Administration")
          )}</div>
          <div><span class="font-semibold">Email</span>: ${escapeHtml(
            String(
              doc.user?.email ?? doc.madinia?.email ?? "contact@madinia.fr"
            )
          )}</div>
          ${
            doc.madinia?.telephone
              ? `<div><span class="font-semibold">Téléphone</span>: ${escapeHtml(
                  String(doc.madinia.telephone)
                )}</div>`
              : ""
          }
          ${
            doc.madinia?.adresse
              ? `<div><span class="font-semibold">Adresse</span>: ${escapeHtml(
                  String(doc.madinia.adresse)
                )}</div>`
              : ""
          }
          ${
            doc.madinia?.siret
              ? `<div><span class="font-semibold">SIRET</span>: ${escapeHtml(
                  String(doc.madinia.siret)
                )}</div>`
              : ""
          }
        </div>
      </div>
      <div>
        <div class="text-[12px] font-bold text-slate-900 uppercase pb-1 border-b border-slate-200">Client</div>
        <div class="mt-3 space-y-1">
          <div><span class="font-semibold">Nom</span>: ${escapeHtml(
            String(doc.client?.nom ?? doc.client?.raison_sociale ?? "")
          )}</div>
          ${
            doc.client?.entreprise?.nom
              ? `<div><span class="font-semibold">Entreprise</span>: ${escapeHtml(
                  String(doc.client.entreprise.nom)
                )}</div>`
              : ""
          }
          ${
            doc.client?.adresse
              ? `<div><span class="font-semibold">Adresse</span>: ${escapeHtml(
                  String(doc.client.adresse)
                )}</div>`
              : ""
          }
          ${
            doc.client?.ville || doc.client?.code_postal
              ? `<div><span class="font-semibold">Ville</span>: ${escapeHtml(
                  String(doc.client?.code_postal ?? "")
                )} ${escapeHtml(String(doc.client?.ville ?? ""))}</div>`
              : ""
          }
        </div>
      </div>
    </section>

    <section class="rounded-md border border-slate-200 overflow-hidden">
      <table class="w-full border-collapse">
        <thead class="bg-slate-100">
          <tr>
            <th class="text-left text-[10px] uppercase tracking-wide text-slate-900 py-2 px-3">Service / Description</th>
            <th class="text-center text-[10px] uppercase tracking-wide text-slate-900 py-2">Qté</th>
            <th class="text-right text-[10px] uppercase tracking-wide text-slate-900 py-2">Prix HT</th>
            <th class="text-right text-[10px] uppercase tracking-wide text-slate-900 py-2">Remise</th>
            <th class="text-right text-[10px] uppercase tracking-wide text-slate-900 py-2">TVA</th>
            <th class="text-right text-[10px] uppercase tracking-wide text-slate-900 py-2 px-3">Total HT</th>
          </tr>
        </thead>
        <tbody class="text-[10px]">
          ${lignesRows}
        </tbody>
      </table>
    </section>

    <section class="mt-4 flex justify-end">
      <div class="w-1/2 border border-slate-200 rounded-md bg-slate-50 p-3 text-[11px]">
        <div class="flex justify-between mb-1"><span>Sous-total HT</span><span>${escapeHtml(
          euro(doc.montant_ht)
        )}</span></div>
        <div class="flex justify-between mb-1 text-slate-600"><span>TVA (${escapeHtml(
          String(doc.taux_tva ?? "0")
        )}%)</span><span>${escapeHtml(euro(doc.montant_tva))}</span></div>
        <div class="border-t border-slate-200 mt-2 pt-2 flex justify-between font-bold"><span>Total TTC</span><span>${escapeHtml(
          euro(doc.montant_ttc)
        )}</span></div>
      </div>
    </section>

    <footer class="pt-3 mt-6 border-t border-slate-200 text-[9px] text-slate-500">
      ${doc.madinia?.name ?? "Madin.IA"} — ${
    doc.madinia?.adresse ?? "1 Chemin du Sud, 97233 Schoelcher"
  } • SIRET ${doc.madinia?.siret ?? "934 303 843 00015"} • ${
    doc.madinia?.email ?? "contact@madinia.fr"
  }
    </footer>
  </main>

  <script>
    // Indiquer à Chromium (Gotenberg) quand tout est prêt
    window.__READY__ = true;
  </script>
 </body>
</html>`;
}

async function generatePdfViaGotenberg(html: string): Promise<Uint8Array> {
  const gotenbergUrl = Deno.env.get("GOTENBERG_URL");
  if (!gotenbergUrl) {
    throw new Error("GOTENBERG_URL manquant");
  }
  const endpoint =
    gotenbergUrl.replace(/\/$/, "") + "/forms/chromium/convert/html";
  const form = new FormData();
  const indexBlob = new Blob([html], { type: "text/html" });
  form.append("files", indexBlob, "index.html");
  form.append(
    "waitForExpression",
    "window.__READY__ === true && !!window.tailwind"
  );
  form.append("emulatedMediaType", "print");
  form.append("paperWidth", "8.27");
  form.append("paperHeight", "11.69");
  const headers: Record<string, string> = {};
  const apiKey = Deno.env.get("GOTENBERG_API_KEY");
  if (apiKey) headers["X-Api-Key"] = apiKey;
  const res = await fetch(endpoint, {
    method: "POST",
    body: form as any,
    headers,
  });
  if (!res.ok) {
    const text = await res.text();
    throw new Error(`Gotenberg error ${res.status}: ${text}`);
  }
  const buf = new Uint8Array(await res.arrayBuffer());
  return buf;
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
  await client.storage
    .updateBucket(bucket, { public: opts.public })
    .catch(() => {});

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
    const expiresIn =
      Number.isFinite(Number(opts.signedTTLSeconds)) &&
      Number(opts.signedTTLSeconds) > 0
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
    let path = url.searchParams.get("path") ?? `devis/${id}.pdf`;
    const isPublic =
      (url.searchParams.get("public") ?? "false").toLowerCase() === "true";
    const signedTTLSeconds = Number(
      url.searchParams.get("signed_ttl") ?? "31536000"
    );

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
    // Si aucun chemin explicite demandé ou si le chemin est encore "devis/<id>.pdf",
    // renommer automatiquement avec le numéro de devis si disponible
    const numeroMaybe = String((devis as any)?.numero_devis ?? "").trim();
    if (numeroMaybe.length > 0) {
      const sanitizedNumero = numeroMaybe.replace(/[^A-Za-z0-9._-]/g, "-");
      const defaultIdPath = `devis/${id}.pdf`;
      if (
        !url.searchParams.has("path") ||
        path.endsWith(`/${id}.pdf`) ||
        path === defaultIdPath
      ) {
        path = `devis/${sanitizedNumero}.pdf`;
      }
    }
    // Charger compléments pour reproduire le rendu React-PDF côté Laravel
    const madinia = await fetchMadinia();
    let client: Client | null = null;
    let entreprise: Entreprise | null = null;
    const clientId = (devis as any)?.client_id ?? null;
    if (clientId) {
      const res = await fetchClientWithEntreprise(Number(clientId));
      client = res.client;
      entreprise = res.entreprise;
      if (client && entreprise) (client as any).entreprise = entreprise;
    }
    const lignes = await fetchLignesForDevis(id);
    (devis as any).lignes = lignes;
    (devis as any).client = client;
    (devis as any).madinia = madinia;

    // Si GOTENBERG_URL est défini, on génère via HTML+Tailwind pour un rendu identique
    let pdfBytes: Uint8Array;
    const gotenbergUrl = Deno.env.get("GOTENBERG_URL");
    if (gotenbergUrl) {
      const html = buildDevisHtml(devis as any);
      pdfBytes = await generatePdfViaGotenberg(html);
    } else {
      // fallback vers pdf-lib (sans Tailwind)
      pdfBytes = await generatePdfFromDevis(devis as any, templateBytes);
    }
    const previousPath = String((devis as any)?.pdf_file ?? "");
    const {
      path: storagePath,
      publicUrl,
      signedUrl,
    } = await uploadToStorage(pdfBytes, bucket, path, {
      public: isPublic,
      signedTTLSeconds,
    });
    // Supprimer l'ancien fichier s'il existait et si le chemin a changé
    try {
      if (previousPath && previousPath !== storagePath) {
        const url = Deno.env.get("SUPABASE_URL");
        const serviceKey = Deno.env.get("SUPABASE_SERVICE_ROLE_KEY");
        if (url && serviceKey) {
          const client = createClient(url, serviceKey, {
            auth: { persistSession: false },
          });
          await client.storage.from(bucket).remove([previousPath]);
        }
      }
    } catch (_) {
      // ignorer les erreurs de suppression
    }
    const finalUrl = isPublic ? publicUrl : signedUrl;
    await updateDevisFileInfo(id, storagePath, finalUrl ?? null);

    return jsonResponse({ ok: true, id, storagePath, publicUrl, signedUrl });
  } catch (e) {
    const message = e instanceof Error ? e.message : String(e);
    return serverError(message);
  }
});
