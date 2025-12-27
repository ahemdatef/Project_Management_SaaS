import { NextResponse } from "next/server";

export function middleware(request) {
  const url = request.nextUrl;
  const searchParams = url.searchParams.toString();
  const path = `${url.pathname}${
    searchParams.length > 0 ? `?${searchParams}` : ""
  }`;

  // Get hostname (e.g. 'apple.localhost:3000' or 'apple.syncro.com')
  const hostname = request.headers.get("host");

  // Define allowed domains (localhost and your production domain)
  const rootDomain = "localhost:3000"; // Change this to your actual domain in prod

  // Check if the current hostname is a subdomain
  const isSubdomain =
    hostname && hostname !== rootDomain && !hostname.includes("www");

  if (isSubdomain) {
    // Extract the subdomain (e.g. 'apple' from 'apple.localhost:3000')
    const subdomain = hostname.split(".")[0];

    // Rewrite the URL to our dynamic route handler
    // User sees: apple.localhost:3000/dashboard
    // Next.js handles: /_sites/apple/dashboard
    return NextResponse.rewrite(
      new URL(`/_sites/${subdomain}${path}`, request.url)
    );
  }

  return NextResponse.next();
}

export const config = {
  matcher: [
    /*
     * Match all paths except for:
     * 1. /api routes
     * 2. /_next (Next.js internals)
     * 3. /_static (inside /public)
     * 4. all root files inside /public (e.g. /favicon.ico)
     */
    "/((?!api/|_next/|_static/|[\\w-]+\\.\\w+).*)",
  ],
};
