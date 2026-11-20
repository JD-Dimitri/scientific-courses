import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { api, ApiError } from './api.js';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  // Au chargement, on demande à l'API si une session est déjà ouverte.
  useEffect(() => {
    api('/me')
      .then((data) => setUser(data.user))
      .catch(() => setUser(null))
      .finally(() => setLoading(false));
  }, []);

  // identifier : adresse e-mail ou identifiant (par exemple « admin »)
  const login = useCallback(async (identifier, password) => {
    try {
      const data = await api('/login', { method: 'POST', body: { identifier, password } });
      setUser(data.user);
      return data.user;
    } catch (error) {
      if (error instanceof ApiError && error.status === 401) {
        throw new ApiError('Identifiant ou mot de passe incorrect. Après 5 essais ratés, attends 15 minutes.', 401);
      }
      throw error;
    }
  }, []);

  const register = useCallback(async (firstName, email, password) => {
    const data = await api('/register', { method: 'POST', body: { firstName, email, password } });
    setUser(data.user);
    return data.user;
  }, []);

  const logout = useCallback(async () => {
    try {
      await api('/logout', { method: 'POST' });
    } finally {
      setUser(null);
    }
  }, []);

  const value = useMemo(() => ({ user, loading, login, register, logout }), [user, loading, login, register, logout]);

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (!context) throw new Error('useAuth doit être utilisé dans <AuthProvider>.');
  return context;
}

/** Protège une page : redirige vers la connexion si l'élève n'est pas connecté. */
export function RequireAuth({ children }) {
  const { user, loading } = useAuth();
  const location = useLocation();

  if (loading) return <p className="loading">Chargement…</p>;
  if (!user) return <Navigate to="/connexion" replace state={{ from: location.pathname }} />;

  return children;
}

/** Protège une page réservée aux administrateurs. */
export function RequireAdmin({ children }) {
  const { user, loading } = useAuth();

  if (loading) return <p className="loading">Chargement…</p>;
  if (!user) return <Navigate to="/connexion" replace />;
  if (!user.isAdmin) return <Navigate to="/" replace />;

  return children;
}
