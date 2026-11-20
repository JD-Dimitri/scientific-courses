import { useState } from 'react';
import { Link } from 'react-router-dom';
import { api } from '../api.js';
import AuthLayout, { Field } from '../components/AuthLayout.jsx';

export default function ForgotPassword() {
  const [email, setEmail] = useState('');
  const [message, setMessage] = useState('');
  const [error, setError] = useState('');
  const [sending, setSending] = useState(false);

  async function handleSubmit(event) {
    event.preventDefault();
    setError('');
    setSending(true);
    try {
      const data = await api('/reset-password/request', { method: 'POST', body: { email } });
      setMessage(data.message);
    } catch (err) {
      setError(err.message);
    } finally {
      setSending(false);
    }
  }

  return (
    <AuthLayout title="Mot de passe oublié" intro="Indique l'adresse de ton compte : tu recevras un lien pour choisir un nouveau mot de passe.">
      {message ? (
        <p className="form-success" role="status">{message} Pense à regarder dans les courriers indésirables. Le lien est valable 1 heure.</p>
      ) : (
        <form onSubmit={handleSubmit} noValidate>
          <Field id="email" label="Adresse e-mail" type="email" autoComplete="email" required value={email} onChange={(e) => setEmail(e.target.value)} />
          {error && <p className="form-error" role="alert">{error}</p>}
          <button type="submit" disabled={sending || !email}>{sending ? 'Envoi…' : 'Recevoir le lien'}</button>
        </form>
      )}
      <p className="auth-links">
        <Link to="/connexion">Retour à la connexion</Link>
      </p>
    </AuthLayout>
  );
}
