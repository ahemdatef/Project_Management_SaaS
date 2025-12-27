import { useMutation } from "@tanstack/react-query";
import api from "../lib/axios";

export const useLogin = () => {
  return useMutation({
    mutationFn: async (credentials) => {
      await api.get("/sanctum/csrf-cookie", {
        baseURL: "http://localhost:8000",
      });
      const response = await api.post("v1/auth/login", credentials);
      return response.data;
    },
  });
};

export const useRegister = () => {
  return useMutation({
    mutationFn: async (userData) => {
      await api.get("/sanctum/csrf-cookie", {
        baseURL: "http://localhost:8000",
      });
      const response = await api.post("v1/auth/register", userData);
      return response.data;
    },
  });
};

export const useLogout = () => {
  return useMutation({
    mutationFn: async () => {
      await api.post("v1/auth/logout");
    },
  });
};
