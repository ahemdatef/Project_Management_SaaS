import axios from "axios";

const api = axios.create({
  baseURL: "http://localhost:8000/api", // Laravel API URL
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
  withCredentials: true, // Important for Sanctum cookies
});

// Request Interceptor
api.interceptors.request.use((config) => {
  // Only run this logic in the browser (client-side)
  if (typeof window !== "undefined") {
    const hostname = window.location.hostname;
    const rootDomain = "localhost"; // Adjust for production

    // Check if we are on a subdomain
    if (
      hostname.includes(rootDomain) &&
      hostname !== `${rootDomain}:3000` &&
      hostname !== rootDomain
    ) {
      const subdomain = hostname.split(".")[0];

      // Attach the header
      config.headers["X-Tenant-Slug"] = subdomain;
    }
  }
  return config;
});

export default api;
