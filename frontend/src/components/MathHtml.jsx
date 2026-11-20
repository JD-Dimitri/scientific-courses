import { useEffect, useRef } from 'react';

/**
 * Affiche un fragment HTML venant de nos fichiers de cours et y fait rendre les formules par MathJax.
 * Le HTML vient uniquement des fichiers du dossier data/, importés par la commande app:import-chapter.
 * Il ne faut jamais y faire passer du texte saisi par un utilisateur.
 */
export default function MathHtml({ html, as: Tag = 'div', className }) {
  const ref = useRef(null);

  useEffect(() => {
    const element = ref.current;
    const mathJax = window.MathJax;
    if (!element || !mathJax?.startup?.promise) return undefined;

    let cancelled = false;
    mathJax.startup.promise
      .then(() => {
        if (cancelled) return undefined;
        mathJax.typesetClear?.([element]);
        return mathJax.typesetPromise([element]);
      })
      .catch(() => {});

    return () => {
      cancelled = true;
    };
  }, [html]);

  return <Tag ref={ref} className={className} dangerouslySetInnerHTML={{ __html: html }} />;
}
