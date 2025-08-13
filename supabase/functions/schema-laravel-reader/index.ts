import { Pool } from "https://deno.land/x/postgres@v0.17.2/mod.ts";

type OrderDir = "asc" | "desc";

function jsonResponse(body: unknown, init: ResponseInit = {}): Response {
  const headers = new Headers(init.headers);
  if (!headers.has("Content-Type")) headers.set("Content-Type", "application/json; charset=utf-8");
  headers.set("Access-Control-Allow-Origin", "*");
  headers.set("Access-Control-Allow-Methods", "GET,OPTIONS");
  headers.set("Access-Control-Allow-Headers", "authorization, content-type");
  const serialized = JSON.stringify(body, (_key, value) =>
    typeof value === "bigint" ? value.toString() : value
  );
  return new Response(serialized, { ...init, headers });
}

function badRequest(message: string): Response {
  return jsonResponse({ error: message }, { status: 400 });
}

function serverError(message: string): Response {
  return jsonResponse({ error: message }, { status: 500 });
}

function getTableFromPath(url: URL, functionSlug: string): string | null {
  const segments = url.pathname.split("/").filter(Boolean);
  const fnIdx = segments.findIndex((s) => s === functionSlug);
  if (fnIdx === -1) return null;
  const table = segments[fnIdx + 1];
  return table ?? null;
}

function quoteIdent(ident: string): string {
  if (!/^[a-zA-Z0-9_]+$/.test(ident)) {
    throw new Error("Invalid identifier");
  }
  return '"' + ident.replace(/"/g, '""') + '"';
}

let pool: Pool | null = null;

Deno.serve(async (req) => {
  if (req.method === "OPTIONS") {
    return jsonResponse({ ok: true }, { status: 200 });
  }

  try {
    const url = new URL(req.url);
    const table = getTableFromPath(url, "schema-laravel-reader");
    if (!table) return badRequest("Missing table name in path. Use /schema-laravel-reader/{table}");

    const limitParam = url.searchParams.get("limit");
    const orderBy = url.searchParams.get("order_by") ?? "id";
    const orderDirParam = (url.searchParams.get("order_dir") ?? "asc").toLowerCase() as OrderDir;

    const limit = Number.isFinite(Number(limitParam)) ? Math.max(1, Math.min(1000, Number(limitParam))) : 100;
    const ascending = orderDirParam === "desc" ? false : true;

    const databaseUrl = Deno.env.get("SUPABASE_DB_URL");
    if (!databaseUrl) {
      return serverError("Missing SUPABASE_DB_URL env variable");
    }

    if (!pool) {
      pool = new Pool(databaseUrl, 3, true);
    }

    const tableIdent = quoteIdent(table);
    const orderIdent = quoteIdent(orderBy);
    const schemaIdent = quoteIdent("laravel");
    const sql = `select * from ${schemaIdent}.${tableIdent} order by ${orderIdent} ${ascending ? "asc" : "desc"} limit ${limit}`;

    const connection = await pool.connect();
    try {
      const result = await connection.queryObject(sql);
      return jsonResponse({ data: result.rows });
    } finally {
      connection.release();
    }
  } catch (e) {
    const message = e instanceof Error ? e.message : String(e);
    return serverError(message);
  }
});


