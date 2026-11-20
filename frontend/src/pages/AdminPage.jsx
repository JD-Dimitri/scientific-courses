import { useEffect, useState } from 'react';
import { api } from '../api.js';
import TopBar from '../components/TopBar.jsx';

const dateFormat = new Intl.DateTimeFormat('fr-FR', { dateStyle: 'medium' });
const formatDate = (value) => (value ? dateFormat.format(new Date(value)) : '—');

export default function AdminPage() {
  const [data, setData] = useState(null);
  const [error, setError] = useState('');

  useEffect(() => {
    api('/admin/students')
      .then(setData)
      .catch((err) => setError(err.message));
  }, []);

  return (
    <>
      <TopBar />
      <main className="page">
        <div className="eyebrow">Administration</div>
        <h1>Élèves et progression</h1>
        {error && <p className="form-error" role="alert">{error}</p>}
        {!data && !error && <p className="loading">Chargement…</p>}
        {data && (
          <>
            <p>{data.students.length} compte{data.students.length > 1 ? 's' : ''}, {data.questionCount} questions au total dans les cours importés.</p>
            <div className="table-wrap">
              <table className="admin-table">
                <thead>
                  <tr>
                    <th scope="col">Prénom</th>
                    <th scope="col">Identifiant</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Rôle</th>
                    <th scope="col">Inscrit le</th>
                    <th scope="col">Faites</th>
                    <th scope="col">Justes</th>
                    <th scope="col">Dernière activité</th>
                  </tr>
                </thead>
                <tbody>
                  {data.students.map((s) => (
                    <tr key={s.id}>
                      <td>{s.firstName}</td>
                      <td>{s.username || '—'}</td>
                      <td>{s.email}</td>
                      <td><span className={s.isAdmin ? 'role role-admin' : 'role'}>{s.isAdmin ? 'Admin' : 'Élève'}</span></td>
                      <td>{formatDate(s.createdAt)}</td>
                      <td className="num-cell">{s.answered}/{data.questionCount}</td>
                      <td className="num-cell">{s.correct}</td>
                      <td>{formatDate(s.lastActivity)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </>
        )}
      </main>
    </>
  );
}
