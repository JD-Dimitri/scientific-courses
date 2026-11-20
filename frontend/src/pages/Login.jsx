import { useState } from 'react';
import { Link, Navigate, useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../auth.jsx';
import AuthLayout, { Field } from '../components/AuthLayout.jsx';

export default function Login() {
  const { user, login } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const [identifier, setIdentifier] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [sending, setSending] = useState(false);

  if (user) return <Navigate to="/" replace />;

  async function handleSubmit(event) {
    event.preventDefault();
    setError('');
    setSending(true);
    try {
      await login(identifier, password);
      navigate(location.state?.from || '/', { replace: true });
    } catch (err) {
      setError(err.message);
    } finally {
      setSending(false);
    }
  }

  return (
    <AuthLayout title="Connexion" intro="Retrouve tes cours et ta progression sur tous tes appareils.">
      <form onSubmit={handleSubmit} noValidate>
        <Field id="identifier" label="Adresse e-mail ou identifiant" type="text" autoComplete="username" autoCapitalize="none" spellCheck={false} required value={identifier} onChange={(e) => setIdentifier(e.target.value)} />
        <Field id="password" label="Mot de passe" type="password" autoComplete="current-password" required value={password} onChange={(e) => setPassword(e.target.value)} />
        {error && <p className="form-error" role="alert">{error}</p>}
        <button type="submit" disabled={sending}>{sending ? 'Connexion…' : 'Se connecter'}</button>
      </form>
      <p className="auth-links">
        <Link to="/mot-de-passe-oublie">Mot de passe oublié ?</Link>
        <Link to="/inscription">Créer un compte élève</Link>
      </p>
    </AuthLayout>
  );
}
