import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../auth.jsx';

export default function TopBar() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  async function handleLogout() {
    await logout();
    navigate('/connexion', { replace: true });
  }

  return (
    <header className="topbar">
      <div className="topbar-inner">
        <Link to="/" className="brand">Scientific Courses</Link>
        <span className="who">{user?.firstName}</span>
        <button type="button" className="ghost" onClick={handleLogout}>Se déconnecter</button>
      </div>
    </header>
  );
}
