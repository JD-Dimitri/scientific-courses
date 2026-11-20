// Petit client pour l'API Symfony. Le cookie de session est envoyé automatiquement
// car le front et l'API partagent la même origine (proxy Vite en développement).

const STATUS_MESSAGES = {
  401: 'Tu dois être connecté.',
  403: 'Accès refusé.',
  404: 'Introuvable.',
  429: 'Trop de tentatives. Réessaie dans quelques minutes.',
  500: 'Le serveur a rencontré une erreur. Réessaie plus tard.',
};

export class ApiError extends Error {
  constructor(message, status, errors = {}) {
    super(message);
    this.status = status;
    this.errors = errors; // erreurs par champ : { email: '...', password: '...' }
  }
}

export async function api(path, { method = 'GET', body } = {}) {
  let response;
  try {
    response = await fetch(`/api${path}`, {
      method,
      credentials: 'same-origin',
      headers: body !== undefined
        ? { 'Content-Type': 'application/json', Accept: 'application/json' }
        : { Accept: 'application/json' },
      body: body !== undefined ? JSON.stringify(body) : undefined,
    });
  } catch {
    throw new ApiError('Impossible de joindre le serveur. Vérifie que l\'API Symfony tourne.', 0);
  }

  if (response.status === 204) return null;

  let data = null;
  try {
    data = await response.json();
  } catch {
    data = null;
  }

  if (!response.ok) {
    const message = data?.error || data?.message || STATUS_MESSAGES[response.status] || `Erreur ${response.status}.`;
    throw new ApiError(message, response.status, data?.errors || {});
  }

  return data;
}
