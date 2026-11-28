"""Génère data/maths/02-algebre-et-equations.json.

Le contenu est écrit ici en Python (chaînes brutes r'...') pour éviter d'échapper le LaTeX à la main.
Lancer depuis la racine du dépôt :  python3 data/maths/src/build_02.py
"""
import json
from pathlib import Path


def Q(level, statement, *correction):
    return {'level': level, 'statementHtml': statement, 'correctionHtml': ''.join(f'<p>{c}</p>' for c in correction)}


def EX(title, statement, *solution):
    return {'title': title, 'statementHtml': f'<p>{statement}</p>', 'solutionHtml': ''.join(f'<p>{s}</p>' for s in solution)}


sections = []

# ---------------------------------------------------------------- 2.1
sections.append({
    'slug': 'calcul-litteral', 'number': '2.1', 'title': 'Calcul littéral',
    'courseHtml': r'''
<p><strong>Développer</strong>, c'est transformer un produit en somme. <strong>Factoriser</strong>, c'est l'inverse : transformer une somme en produit. Les deux écritures ont toujours la même valeur.</p>
<div class="regle">
<p>Distributivité : \(k(a+b)=ka+kb\) et \((a+b)(c+d)=ac+ad+bc+bd\).</p>
<p>Identités remarquables :</p>
\[(a+b)^2=a^2+2ab+b^2\qquad(a-b)^2=a^2-2ab+b^2\qquad(a+b)(a-b)=a^2-b^2\]
<p>Puissances : \(a^m\times a^n=a^{m+n}\), \(\dfrac{a^m}{a^n}=a^{m-n}\), \((a^m)^n=a^{mn}\), \((ab)^n=a^nb^n\).</p>
</div>
<p>Pour factoriser, on cherche d'abord un <strong>facteur commun</strong>, puis une <strong>identité remarquable</strong>.</p>
<p>Fractions : \(\dfrac ab+\dfrac cd=\dfrac{ad+bc}{bd}\). Pour simplifier une fraction, on factorise le numérateur et le dénominateur, puis on simplifie les <em>facteurs</em> communs. On ne simplifie jamais un terme d'une somme : \(\dfrac{x+3}{x}\) ne vaut pas 3. On note aussi les valeurs interdites, qui annulent le dénominateur.</p>
'''.strip(),
    'examples': [
        EX('Exemple 1', r'Développer \((2x+3)(x-4)\).',
           r'\(2x\times x+2x\times(-4)+3\times x+3\times(-4)=2x^2-8x+3x-12\).',
           r'Résultat : \(2x^2-5x-12\).'),
        EX('Exemple 2', r'Factoriser \(9x^2-12x+4\).',
           r'\(9x^2=(3x)^2\), \(4=2^2\) et \(12x=2\times3x\times2\) : c\'est la forme \(a^2-2ab+b^2\).',
           r'\(9x^2-12x+4=(3x-2)^2\).'),
        EX('Exemple 3', r'Simplifier \(\dfrac{x^2-9}{x^2+3x}\).',
           r'On factorise : \(\dfrac{(x-3)(x+3)}{x(x+3)}\). Valeurs interdites : \(0\) et \(-3\).',
           r'On simplifie le facteur \(x+3\) : \(\dfrac{x-3}{x}\), pour \(x\neq0\) et \(x\neq-3\).'),
    ],
    'questions': [
        Q(1, r'Développe \(3(x-5)\).', r'\(3x-15\).'),
        Q(1, r'Développe \(-2(4-x)\).', r'\(-8+2x\). Attention : \(-2\times(-x)=+2x\).'),
        Q(1, r'Développe \((x+2)(x+5)\).', r'\(x^2+5x+2x+10=x^2+7x+10\).'),
        Q(1, r'Développe \((x+4)^2\).', r'\(x^2+8x+16\). Le double produit \(2\times x\times4\) ne doit pas être oublié.'),
        Q(1, r'Développe \((x-3)(x+3)\).', r'\(x^2-9\).'),
        Q(1, r'Factorise \(5x+15\).', r'Facteur commun 5 : \(5(x+3)\).'),
        Q(1, r'Factorise \(x^2-7x\).', r'Facteur commun \(x\) : \(x(x-7)\).'),
        Q(2, r'Développe \((3x-1)(2x+5)\).', r'\(6x^2+15x-2x-5=6x^2+13x-5\).'),
        Q(2, r'Développe \((2x-5)^2\).', r'\(4x^2-20x+25\).'),
        Q(2, r'Factorise \(x^2-25\).', r'\((x-5)(x+5)\).'),
        Q(2, r'Factorise \(4x^2+12x+9\).', r'\((2x)^2+2\times2x\times3+3^2=(2x+3)^2\).'),
        Q(2, r'Factorise \((x+1)(x-2)+(x+1)(2x+3)\).', r'Facteur commun \((x+1)\) : \((x+1)\big[(x-2)+(2x+3)\big]=(x+1)(3x+1)\).'),
        Q(2, r'Réduis au même dénominateur \(\dfrac1x+\dfrac2{x+1}\).', r'\(\dfrac{x+1+2x}{x(x+1)}=\dfrac{3x+1}{x(x+1)}\), pour \(x\neq0\) et \(x\neq-1\).'),
        Q(2, r'Simplifie \(\dfrac{x^3\times x^5}{x^2}\).', r'\(x^{3+5-2}=x^6\).'),
        Q(2, r'Simplifie \((2a^2b)^3\).', r'\(2^3\times(a^2)^3\times b^3=8a^6b^3\).'),
        Q(2, r'Factorise \(16x^2-(x+3)^2\).', r'Forme \(A^2-B^2\) avec \(A=4x\), \(B=x+3\) : \((4x-x-3)(4x+x+3)=(3x-3)(5x+3)\).', r'On peut encore sortir 3 : \(3(x-1)(5x+3)\).'),
        Q(3, r'Simplifie \(\dfrac{x^2-4}{x^2-4x+4}\).', r'\(\dfrac{(x-2)(x+2)}{(x-2)^2}=\dfrac{x+2}{x-2}\), pour \(x\neq2\).'),
        Q(3, r'Réduis \(\dfrac2{x-1}-\dfrac1{x+1}\).', r'\(\dfrac{2(x+1)-(x-1)}{(x-1)(x+1)}=\dfrac{x+3}{x^2-1}\), pour \(x\neq\pm1\).'),
        Q(3, r'Développe \((x+1)^3\).', r'\((x+1)^2(x+1)=(x^2+2x+1)(x+1)=x^3+3x^2+3x+1\).'),
        Q(3, r'Montre que pour tout entier \(n\), \((n+1)^2-(n-1)^2=4n\).', r'\((n^2+2n+1)-(n^2-2n+1)=4n\).', r'Conséquence : la différence des carrés de deux entiers qui diffèrent de 2 est toujours un multiple de 4.'),
    ],
})

# ---------------------------------------------------------------- 2.2
sections.append({
    'slug': 'equations', 'number': '2.2', 'title': 'Équations du premier degré, produit et quotient',
    'courseHtml': r'''
<p>Résoudre une équation, c'est trouver toutes les valeurs de l'inconnue qui la rendent vraie. On transforme l'équation en une équation équivalente, en faisant la même opération sur les deux membres.</p>
<div class="regle">
<ul>
<li><strong>Premier degré</strong> : on regroupe les \(x\) d'un côté, les nombres de l'autre. \(ax+b=0\iff x=-\dfrac ba\) si \(a\neq0\).</li>
<li><strong>Équation produit</strong> : \(A\times B=0\iff A=0\) ou \(B=0\).</li>
<li><strong>Équation quotient</strong> : \(\dfrac AB=0\iff A=0\) et \(B\neq0\). On cherche d'abord les valeurs interdites.</li>
<li>\(x^2=k\) : deux solutions \(\pm\sqrt k\) si \(k\gt0\), une seule (0) si \(k=0\), aucune si \(k\lt0\).</li>
</ul>
</div>
<p>Piège classique : ne jamais diviser les deux membres par une expression qui contient \(x\), car elle peut être nulle et on perd une solution. On factorise à la place.</p>
'''.strip(),
    'examples': [
        EX('Exemple 1', r'Résoudre \(5x-3=2x+9\).', r'\(5x-2x=9+3\), donc \(3x=12\) et \(x=4\).', r'Vérification : \(5\times4-3=17\) et \(2\times4+9=17\).'),
        EX('Exemple 2', r'Résoudre \((2x-1)(x+3)=0\).', r'Un produit est nul si l\'un des facteurs est nul : \(2x-1=0\) ou \(x+3=0\).', r'\(S=\left\{-3\,;\dfrac12\right\}\).'),
        EX('Exemple 3', r'Résoudre \(\dfrac{x-2}{x+1}=0\).', r'Valeur interdite : \(x=-1\). Le quotient est nul quand le numérateur l\'est : \(x=2\).', r'2 n\'est pas interdite : \(S=\{2\}\).'),
    ],
    'questions': [
        Q(1, r'Résous \(x+7=12\).', r'\(x=5\).'),
        Q(1, r'Résous \(3x=-18\).', r'\(x=-6\).'),
        Q(1, r'Résous \(4x-5=11\).', r'\(4x=16\), donc \(x=4\).'),
        Q(1, r'Résous \(2x+3=x-4\).', r'\(x=-7\).'),
        Q(1, r'Résous \((x-3)(x+5)=0\).', r'\(S=\{-5\,;3\}\).'),
        Q(1, r'Résous \(x^2=49\).', r'\(S=\{-7\,;7\}\). On n\'oublie pas la solution négative.'),
        Q(1, r'Résous \(\dfrac x4=3\).', r'\(x=12\).'),
        Q(2, r'Résous \(3(x-2)=2(x+1)\).', r'\(3x-6=2x+2\), donc \(x=8\).'),
        Q(2, r'Résous \(\dfrac x2+\dfrac x3=10\).', r'\(\dfrac{3x+2x}6=10\), donc \(5x=60\) et \(x=12\).'),
        Q(2, r'Résous \((2x+1)(3-x)=0\).', r'\(2x+1=0\) ou \(3-x=0\) : \(S=\left\{-\dfrac12\,;3\right\}\).'),
        Q(2, r'Résous \(x^2=5x\).', r'On factorise au lieu de diviser par \(x\) : \(x^2-5x=0\iff x(x-5)=0\).', r'\(S=\{0\,;5\}\). En divisant par \(x\), on aurait perdu la solution 0.'),
        Q(2, r'Résous \(x^2=-4\) dans ℝ.', r'Un carré n\'est jamais négatif : aucune solution réelle, \(S=\varnothing\).'),
        Q(2, r'Résous \(\dfrac{x+4}{x-2}=0\).', r'Valeur interdite : 2. Numérateur nul : \(x=-4\). \(S=\{-4\}\).'),
        Q(2, r'Résous \(\dfrac{x-1}{x+3}=2\).', r'Valeur interdite : \(-3\). On multiplie par \(x+3\) : \(x-1=2x+6\), donc \(x=-7\).', r'\(-7\) n\'est pas interdite : \(S=\{-7\}\).'),
        Q(2, r'Résous \(4x^2-9=0\).', r'\((2x-3)(2x+3)=0\) : \(S=\left\{-\dfrac32\,;\dfrac32\right\}\).'),
        Q(3, r'Résous \((x+1)^2=(2x-3)^2\).', r'On passe tout à gauche et on factorise \(A^2-B^2\) : \((x+1-2x+3)(x+1+2x-3)=0\), soit \((4-x)(3x-2)=0\).', r'\(S=\left\{\dfrac23\,;4\right\}\).'),
        Q(3, r'Résous \(\dfrac1x+\dfrac1{x+2}=0\).', r'Valeurs interdites : 0 et \(-2\). \(\dfrac{x+2+x}{x(x+2)}=0\iff2x+2=0\).', r'\(S=\{-1\}\).'),
        Q(3, r'Résous en \(x\) l\'équation \(ax+b=cx+d\), avec \(a\neq c\).', r'\((a-c)x=d-b\), donc \(x=\dfrac{d-b}{a-c}\). La condition \(a\neq c\) permet de diviser.'),
        Q(3, r'Un article coûte 96 € TTC, avec une TVA de 20 %. Quel est son prix hors taxe ?', r'\(1{,}2\,x=96\), donc \(x=80\) €.', r'Erreur fréquente : retirer 20 % de 96 donne 76,80 €, ce qui est faux.'),
        Q(3, r'On ajoute de l\'eau à 50 mL d\'une solution à 0,20 mol/L pour obtenir une concentration de 0,050 mol/L. Quel volume d\'eau faut-il ajouter ?', r'La quantité de matière ne change pas : \(0{,}20\times50=0{,}050\times V\), donc \(V=200\) mL.', r'Il faut ajouter \(200-50=150\) mL d\'eau.'),
    ],
})

# ---------------------------------------------------------------- 2.3
sections.append({
    'slug': 'second-degre', 'number': '2.3', 'title': 'Second degré',
    'courseHtml': r'''
<p>Un trinôme du second degré s'écrit \(f(x)=ax^2+bx+c\), avec \(a\neq0\). Sa courbe est une parabole, tournée vers le haut si \(a\gt0\), vers le bas si \(a\lt0\).</p>
<div class="regle">
<p>Forme canonique : \(f(x)=a(x-\alpha)^2+\beta\), avec \(\alpha=-\dfrac b{2a}\) et \(\beta=f(\alpha)\). Le sommet de la parabole est \((\alpha\,;\beta)\).</p>
<p>Discriminant : \(\Delta=b^2-4ac\).</p>
<ul>
<li>\(\Delta\gt0\) : deux racines \(x_{1,2}=\dfrac{-b\pm\sqrt\Delta}{2a}\), et \(f(x)=a(x-x_1)(x-x_2)\).</li>
<li>\(\Delta=0\) : une racine double \(-\dfrac b{2a}\), et \(f(x)=a(x-x_0)^2\).</li>
<li>\(\Delta\lt0\) : pas de racine réelle, pas de factorisation dans ℝ.</li>
</ul>
<p>Somme et produit des racines : \(x_1+x_2=-\dfrac ba\) et \(x_1x_2=\dfrac ca\).</p>
</div>
<p>Avant de calculer \(\Delta\), regarde si une factorisation directe suffit : facteur commun (\(x^2-9x\)) ou identité remarquable (\(x^2-16\)).</p>
'''.strip(),
    'examples': [
        EX('Exemple 1', r'Résoudre \(x^2-6x+5=0\) et factoriser.', r'\(\Delta=36-20=16\), donc \(x_{1,2}=\dfrac{6\pm4}{2}\) : 1 et 5.', r'\(x^2-6x+5=(x-1)(x-5)\).'),
        EX('Exemple 2', r'Mettre \(x^2+4x+1\) sous forme canonique.', r'\(x^2+4x\) est le début de \((x+2)^2=x^2+4x+4\).', r'Donc \(x^2+4x+1=(x+2)^2-4+1=(x+2)^2-3\). Sommet : \((-2\,;-3)\).'),
        EX('Exemple 3', r'Résoudre \(2x^2+3x+5=0\).', r'\(\Delta=9-40=-31\lt0\).', r'Aucune solution réelle.'),
    ],
    'questions': [
        Q(1, r'Calcule le discriminant de \(x^2+2x-3\).', r'\(\Delta=4+12=16\).'),
        Q(1, r'Résous \(x^2+2x-3=0\).', r'\(x=\dfrac{-2\pm4}2\) : \(S=\{-3\,;1\}\).'),
        Q(1, r'Résous \(x^2-4x+4=0\).', r'\(\Delta=0\) : racine double \(x=2\). C\'est aussi \((x-2)^2=0\).'),
        Q(1, r'Résous \(x^2+x+1=0\) dans ℝ.', r'\(\Delta=1-4=-3\lt0\) : aucune solution réelle.'),
        Q(1, r'Donne le sommet de la parabole \(y=x^2-6x+2\).', r'\(\alpha=\dfrac62=3\) et \(\beta=9-18+2=-7\). Sommet \((3\,;-7)\).'),
        Q(1, r'Résous \(x^2-9x=0\), sans discriminant.', r'\(x(x-9)=0\) : \(S=\{0\,;9\}\).'),
        Q(1, r'Factorise \(x^2-5x+6\).', r'Racines 2 et 3 (somme 5, produit 6) : \((x-2)(x-3)\).'),
        Q(2, r'Résous \(2x^2-7x+3=0\).', r'\(\Delta=49-24=25\), \(x=\dfrac{7\pm5}4\) : \(S=\left\{\dfrac12\,;3\right\}\).'),
        Q(2, r'Résous \(3x^2+6x+3=0\).', r'\(\Delta=36-36=0\) : \(x=-\dfrac6{6}=-1\). On pouvait aussi diviser par 3 : \((x+1)^2=0\).'),
        Q(2, r'Mets \(x^2-8x+10\) sous forme canonique.', r'\((x-4)^2=x^2-8x+16\), donc \(x^2-8x+10=(x-4)^2-6\).'),
        Q(2, r'Mets \(2x^2+8x-1\) sous forme canonique.', r'\(2(x^2+4x)-1=2\big[(x+2)^2-4\big]-1=2(x+2)^2-9\).'),
        Q(2, r'Factorise \(2x^2-x-1\).', r'\(\Delta=1+8=9\), racines \(\dfrac{1\pm3}4\) : 1 et \(-\dfrac12\).', r'\(2(x-1)\left(x+\dfrac12\right)=(x-1)(2x+1)\).'),
        Q(2, r'Trouve deux nombres dont la somme vaut 10 et le produit 21.', r'Ce sont les racines de \(x^2-10x+21=0\) : \(\Delta=100-84=16\), donc 3 et 7.'),
        Q(2, r'Résous \(-x^2+4x-3=0\).', r'On multiplie par \(-1\) : \(x^2-4x+3=0\), racines 1 et 3.'),
        Q(2, r'Pour quelles valeurs de \(m\) l\'équation \(x^2+mx+4=0\) a-t-elle une racine double ?', r'\(\Delta=m^2-16=0\iff m=4\) ou \(m=-4\).'),
        Q(3, r'Résous \(x^4-5x^2+4=0\).', r'On pose \(X=x^2\) : \(X^2-5X+4=0\), donc \(X=1\) ou \(X=4\).', r'\(x^2=1\) ou \(x^2=4\) : \(S=\{-2\,;-1\,;1\,;2\}\).'),
        Q(3, r'Un rectangle a un périmètre de 20 cm et une aire de 24 cm². Quelles sont ses dimensions ?', r'Largeur \(x\), longueur \(10-x\) : \(x(10-x)=24\iff x^2-10x+24=0\).', r'\(\Delta=4\), racines 4 et 6 : le rectangle mesure 4 cm sur 6 cm.'),
        Q(3, r'Résous \(x^2-2\sqrt3\,x+3=0\).', r'On reconnaît \((x-\sqrt3)^2=0\) : racine double \(x=\sqrt3\).'),
        Q(3, r'Une balle est lancée vers le haut : sa hauteur vaut \(h(t)=-5t^2+20t\) mètres. Quand retombe-t-elle au sol, et quelle hauteur maximale atteint-elle ?', r'\(-5t(t-4)=0\) : elle retombe à \(t=4\) s.', r'Le sommet est en \(t=\dfrac{-20}{2\times(-5)}=2\) s, et \(h(2)=-20+40=20\) m.'),
        Q(3, r'Pour quelles valeurs de \(m\) l\'équation \(x^2-2x+m=0\) a-t-elle deux solutions réelles distinctes ?', r'\(\Delta=4-4m\gt0\iff m\lt1\).'),
    ],
})

# ---------------------------------------------------------------- 2.4
sections.append({
    'slug': 'inequations', 'number': '2.4', 'title': 'Inéquations et tableaux de signes',
    'courseHtml': r'''
<p>Une inéquation se résout comme une équation, avec une règle en plus : <strong>multiplier ou diviser par un nombre négatif inverse le sens</strong> de l'inégalité. La solution est en général un intervalle ou une réunion d'intervalles.</p>
<div class="regle">
<ul>
<li>Signe de \(ax+b\) : il vaut 0 en \(-\dfrac ba\) ; à droite de cette valeur, il a le signe de \(a\), à gauche le signe contraire.</li>
<li>Signe de \(ax^2+bx+c\) : il a le signe de \(a\) partout, <em>sauf entre les racines</em> quand il y en a deux.</li>
<li>Produit ou quotient : on dresse un <strong>tableau de signes</strong>, une ligne par facteur, et on applique la règle des signes. Les valeurs interdites sont marquées d'une double barre.</li>
</ul>
</div>
<p>Méthode : tout passer d'un côté pour comparer à 0, factoriser, faire le tableau, lire la réponse. On ne multiplie jamais les deux membres par une expression dont on ne connaît pas le signe.</p>
'''.strip(),
    'examples': [
        EX('Exemple 1', r'Résoudre \(3-2x\gt7\).', r'\(-2x\gt4\). On divise par \(-2\), qui est négatif : le sens change.', r'\(x\lt-2\), soit \(S=\,]-\infty\,;-2[\).'),
        EX('Exemple 2', r'Résoudre \(x^2-x-6\le0\).', r'Racines : \(\Delta=25\), donc \(-2\) et 3. Comme \(a=1\gt0\), le trinôme est négatif entre les racines.', r'\(S=[-2\,;3]\).'),
        EX('Exemple 3', r'Résoudre \(\dfrac{x-1}{x+2}\ge0\).', r'\(x-1\) s\'annule en 1, \(x+2\) en \(-2\) (valeur interdite). Le tableau donne : positif avant \(-2\), négatif entre \(-2\) et 1, positif après 1.', r'\(S=\,]-\infty\,;-2[\,\cup\,[1\,;+\infty[\). Le 1 est inclus (quotient nul), le \(-2\) exclu (interdit).'),
    ],
    'questions': [
        Q(1, r'Résous \(x+3\lt8\).', r'\(x\lt5\).'),
        Q(1, r'Résous \(2x\ge10\).', r'\(x\ge5\).'),
        Q(1, r'Résous \(-3x\lt12\).', r'On divise par \(-3\) et on change le sens : \(x\gt-4\).'),
        Q(1, r'Résous \(5x-1\gt2x+8\).', r'\(3x\gt9\), donc \(x\gt3\).'),
        Q(1, r'Donne le signe de \(2x-6\) selon \(x\).', r'Nul en 3, négatif si \(x\lt3\), positif si \(x\gt3\).'),
        Q(1, r'Donne le signe de \(-x+4\) selon \(x\).', r'Nul en 4, positif si \(x\lt4\), négatif si \(x\gt4\) (car \(a=-1\lt0\)).'),
        Q(1, r'Résous \(x^2\gt0\).', r'Un carré est positif, et nul seulement en 0 : \(S=\mathbb R\setminus\{0\}\).'),
        Q(2, r'Résous \((x-2)(x+1)\gt0\).', r'Racines \(-1\) et 2, trinôme de coefficient \(a=1\gt0\) : positif à l\'extérieur des racines.', r'\(S=\,]-\infty\,;-1[\,\cup\,]2\,;+\infty[\).'),
        Q(2, r'Résous \(x^2-9\le0\).', r'Négatif entre les racines \(-3\) et 3 : \(S=[-3\,;3]\).'),
        Q(2, r'Résous \(x^2+1\gt0\).', r'Toujours vrai : \(S=\mathbb R\).'),
        Q(2, r'Résous \(-x^2+5x-4\ge0\).', r'Racines 1 et 4. Ici \(a=-1\lt0\) : le trinôme est positif entre les racines.', r'\(S=[1\,;4]\).'),
        Q(2, r'Résous \(x^2\le2x\).', r'\(x^2-2x\le0\iff x(x-2)\le0\) : \(S=[0\,;2]\).'),
        Q(2, r'Résous \(\dfrac{x+3}{x-1}\lt0\).', r'Tableau : nul en \(-3\), interdit en 1, négatif entre les deux.', r'\(S=\,]-3\,;1[\).'),
        Q(2, r'Résous \(2x^2-3x-2\gt0\).', r'\(\Delta=9+16=25\), racines \(\dfrac{3\pm5}4\) : 2 et \(-\dfrac12\). Positif à l\'extérieur.', r'\(S=\,\left]-\infty\,;-\dfrac12\right[\,\cup\,]2\,;+\infty[\).'),
        Q(2, r'Résous \(\dfrac1x\ge2\).', r'\(\dfrac1x-2\ge0\iff\dfrac{1-2x}x\ge0\). Nul en \(\dfrac12\), interdit en 0.', r'Tableau : positif entre 0 et \(\dfrac12\). \(S=\,\left]0\,;\dfrac12\right]\).', r'On ne multiplie pas par \(x\), dont on ne connaît pas le signe.'),
        Q(3, r'Résous \((x-1)^2(x+2)\ge0\).', r'\((x-1)^2\ge0\) toujours, donc le signe est celui de \(x+2\), sauf en \(x=1\) où le produit est nul (donc solution).', r'\(S=[-2\,;+\infty[\).'),
        Q(3, r'Résous \(\dfrac{2x-1}{x+3}\le1\).', r'\(\dfrac{2x-1-(x+3)}{x+3}\le0\iff\dfrac{x-4}{x+3}\le0\).', r'\(S=\,]-3\,;4]\).'),
        Q(3, r'Pour quelles valeurs de \(x\) l\'expression \(\sqrt{6-x-x^2}\) est-elle définie ?', r'Il faut \(-x^2-x+6\ge0\iff x^2+x-6\le0\). Racines \(-3\) et 2.', r'\(x\in[-3\,;2]\).'),
        Q(3, r'Abonnement A : 20 € plus 2 € par séance. Formule B : 5 € par séance. À partir de combien de séances A est-il moins cher ?', r'\(20+2n\lt5n\iff3n\gt20\iff n\gt6{,}67\).', r'À partir de 7 séances.'),
        Q(3, r'Résous \(x^3\gt x\).', r'\(x^3-x=x(x-1)(x+1)\gt0\). Tableau à trois facteurs, racines \(-1\), 0, 1.', r'\(S=\,]-1\,;0[\,\cup\,]1\,;+\infty[\).'),
    ],
})

# ---------------------------------------------------------------- 2.5
sections.append({
    'slug': 'systemes', 'number': '2.5', 'title': 'Systèmes linéaires',
    'courseHtml': r'''
<p>Un système linéaire réunit plusieurs équations du premier degré qui doivent être vraies en même temps. Avec deux inconnues, chaque équation est une droite, et la solution est leur point d'intersection.</p>
<div class="regle">
<ul>
<li><strong>Substitution</strong> : on exprime une inconnue en fonction de l'autre dans une équation, puis on remplace dans l'autre.</li>
<li><strong>Combinaison</strong> : on multiplie les équations pour que l'addition ou la soustraction élimine une inconnue.</li>
<li>Pour \(ax+by=e\) et \(cx+dy=f\) : si \(ad-bc\neq0\), une solution unique. Si \(ad-bc=0\), soit aucune solution (droites parallèles), soit une infinité (même droite).</li>
<li>Trois inconnues : on élimine la même inconnue dans deux paires d'équations, on résout le système à deux inconnues obtenu, puis on remonte. C'est la méthode du pivot de Gauss.</li>
</ul>
</div>
<p>Toujours vérifier la solution dans <em>toutes</em> les équations de départ. Le nombre \(ad-bc\) est le déterminant, qu'on retrouve au chapitre sur les matrices.</p>
'''.strip(),
    'examples': [
        EX('Exemple 1', r'Résoudre \(x+y=7\) et \(x-y=1\).', r'En additionnant : \(2x=8\), donc \(x=4\). Puis \(y=7-4=3\).', r'Solution \((4\,;3)\).'),
        EX('Exemple 2', r'Résoudre \(2x+3y=12\) et \(x-y=1\).', r'La seconde donne \(x=1+y\). Dans la première : \(2+2y+3y=12\), donc \(y=2\) et \(x=3\).', r'Solution \((3\,;2)\).'),
        EX('Exemple 3', r'Résoudre \(x+y+z=6\), \(x-y+z=2\), \(2x+y-z=1\).', r'Première moins deuxième : \(2y=4\), donc \(y=2\). Il reste \(x+z=4\) et \(2x-z=-1\).', r'En additionnant : \(3x=3\), donc \(x=1\) et \(z=3\). Vérification dans la troisième : \(2+2-3=1\). Solution \((1\,;2\,;3)\).'),
    ],
    'questions': [
        Q(1, r'Résous \(x+y=10\) et \(x-y=4\).', r'Addition : \(2x=14\), \(x=7\), \(y=3\).'),
        Q(1, r'Résous \(y=2x\) et \(x+y=9\).', r'\(x+2x=9\) : \(x=3\), \(y=6\).'),
        Q(1, r'Résous \(x=3\) et \(2x+y=10\).', r'\(6+y=10\) : \(y=4\).'),
        Q(1, r'Résous \(2x+y=8\) et \(x-y=1\).', r'Addition : \(3x=9\), \(x=3\), \(y=2\).'),
        Q(1, r'Le couple \((2\,;-1)\) est-il solution de \(3x+2y=4\) et \(x-y=3\) ?', r'\(6-2=4\) et \(2+1=3\) : oui.'),
        Q(1, r'Résous \(x+2y=7\) et \(x=1\).', r'\(1+2y=7\) : \(y=3\).'),
        Q(1, r'Résous \(3x-y=5\) et \(y=1\).', r'\(3x=6\) : \(x=2\).'),
        Q(2, r'Résous \(3x+2y=16\) et \(2x-y=6\).', r'\(y=2x-6\). Puis \(3x+4x-12=16\), donc \(x=4\) et \(y=2\).'),
        Q(2, r'Résous \(5x+3y=1\) et \(2x+3y=-5\).', r'Soustraction : \(3x=6\), \(x=2\). Puis \(3y=1-10\), \(y=-3\).'),
        Q(2, r'Résous \(2x+4y=6\) et \(x+2y=3\).', r'La première est le double de la seconde : c\'est la même droite. Infinité de solutions : tous les couples \((3-2y\,;y)\).'),
        Q(2, r'Résous \(x+y=2\) et \(x+y=5\).', r'Contradiction : aucune solution. Les deux droites sont parallèles.'),
        Q(2, r'Le système \(2x+3y=1\), \(4x+6y=5\) a-t-il une solution unique ?', r'\(ad-bc=2\times6-3\times4=0\) : non. Ici \(4x+6y\) vaudrait 2, pas 5 : aucune solution.'),
        Q(2, r'Résous \(\dfrac x2+\dfrac y3=4\) et \(x-y=3\).', r'On multiplie la première par 6 : \(3x+2y=24\). Avec \(x=y+3\) : \(5y+9=24\), \(y=3\), \(x=6\).'),
        Q(2, r'Au cinéma, une place adulte coûte 9 € et une place enfant 6 €. On achète 7 places pour 51 €. Combien de places de chaque ?', r'\(a+e=7\) et \(9a+6e=51\). Avec \(e=7-a\) : \(3a+42=51\), donc \(a=3\) et \(e=4\).'),
        Q(2, r'Trouve l\'équation \(y=ax+b\) de la droite passant par \((1\,;5)\) et \((3\,;9)\).', r'\(a+b=5\) et \(3a+b=9\). Soustraction : \(2a=4\), \(a=2\), \(b=3\). Droite \(y=2x+3\).'),
        Q(3, r'Résous \(x+y+z=6\), \(x-y=1\), \(y-z=-2\).', r'\(x=y+1\) et \(z=y+2\). Dans la première : \(3y+3=6\), donc \(y=1\).', r'Solution \((2\,;1\,;3)\).'),
        Q(3, r'Résous \(x+2y+z=8\), \(2x-y+z=3\), \(x+y-z=0\).', r'On élimine \(x\) : \(L_2-2L_1\) donne \(-5y-z=-13\) ; \(L_3-L_1\) donne \(-y-2z=-8\).', r'De la seconde : \(y=8-2z\). Dans la première : \(-40+10z-z=-13\), donc \(z=3\), \(y=2\), puis \(x=8-4-3=1\).', r'Solution \((1\,;2\,;3)\).'),
        Q(3, r'Pour quelle valeur de \(m\) le système \(x+my=1\), \(2x+4y=3\) n\'a-t-il pas de solution unique ? Que se passe-t-il alors ?', r'\(ad-bc=4-2m=0\iff m=2\).', r'Avec \(m=2\) : \(x+2y=1\) donne \(2x+4y=2\), incompatible avec 3. Aucune solution.'),
        Q(3, r'On mélange une solution A à 10 g/L et une solution B à 40 g/L pour obtenir 300 mL à 20 g/L. Quels volumes de A et de B faut-il ?', r'\(a+b=300\) et \(10a+40b=20\times300=6\,000\) (en mg, volumes en mL).', r'Avec \(b=300-a\) : \(10a+12\,000-40a=6\,000\), donc \(a=200\) mL et \(b=100\) mL.'),
        Q(3, r'Trouve la parabole \(y=ax^2+bx+c\) qui passe par \((0\,;1)\), \((1\,;2)\) et \((-1\,;4)\).', r'\(c=1\), puis \(a+b=1\) et \(a-b=3\).', r'\(a=2\), \(b=-1\) : \(y=2x^2-x+1\).'),
    ],
})

# ---------------------------------------------------------------- Examen
exam = [
    Q(1, r'Développe \((x+3)(x-2)\). <em>(2.1)</em>', r'\(x^2-2x+3x-6=x^2+x-6\).'),
    Q(1, r'Résous \(7x-4=3x+8\). <em>(2.2)</em>', r'\(4x=12\), \(x=3\).'),
    Q(1, r'Factorise \(x^2-16\). <em>(2.1)</em>', r'\((x-4)(x+4)\).'),
    Q(1, r'Résous \(x+y=8\) et \(x-y=2\). <em>(2.5)</em>', r'\(x=5\), \(y=3\).'),
    Q(1, r'Résous \(-2x+6\ge0\). <em>(2.4)</em>', r'\(-2x\ge-6\), donc \(x\le3\) (le sens change).'),
    Q(2, r'Développe \((3x+2)^2\). <em>(2.1)</em>', r'\(9x^2+12x+4\).'),
    Q(2, r'Résous \((x-4)(2x+1)=0\). <em>(2.2)</em>', r'\(S=\left\{-\dfrac12\,;4\right\}\).'),
    Q(2, r'Résous \(x^2-3x-10=0\). <em>(2.3)</em>', r'\(\Delta=9+40=49\), \(x=\dfrac{3\pm7}2\) : \(S=\{-2\,;5\}\).'),
    Q(2, r'Mets \(x^2+6x+5\) sous forme canonique. <em>(2.3)</em>', r'\((x+3)^2-9+5=(x+3)^2-4\).'),
    Q(2, r'Résous \(x^2-x-6\gt0\). <em>(2.4)</em>', r'Racines \(-2\) et 3, \(a\gt0\) : \(S=\,]-\infty\,;-2[\,\cup\,]3\,;+\infty[\).'),
    Q(2, r'Simplifie \(\dfrac{x^2-1}{x+1}\). <em>(2.1)</em>', r'\(\dfrac{(x-1)(x+1)}{x+1}=x-1\), pour \(x\neq-1\).'),
    Q(2, r'Résous \(\dfrac{2x-6}{x+5}=0\). <em>(2.2)</em>', r'Valeur interdite \(-5\). \(2x-6=0\) : \(S=\{3\}\).'),
    Q(2, r'Résous \(2x+y=7\) et \(x+3y=11\). <em>(2.5)</em>', r'\(y=7-2x\), puis \(x+21-6x=11\) : \(x=2\), \(y=3\).'),
    Q(2, r'Pour quelles valeurs de \(m\) l\'équation \(x^2+6x+m=0\) a-t-elle deux solutions distinctes ? <em>(2.3)</em>', r'\(\Delta=36-4m\gt0\iff m\lt9\).'),
    Q(3, r'Résous \(\dfrac x{x-1}\ge2\). <em>(2.4)</em>', r'\(\dfrac{x-2(x-1)}{x-1}\ge0\iff\dfrac{2-x}{x-1}\ge0\).', r'\(S=\,]1\,;2]\).'),
    Q(3, r'Résous \((3x-1)^2=(x+5)^2\). <em>(2.2)</em>', r'\((3x-1-x-5)(3x-1+x+5)=0\iff(2x-6)(4x+4)=0\).', r'\(S=\{-1\,;3\}\).'),
    Q(3, r'Résous \(2x^4-3x^2+1=0\). <em>(2.3)</em>', r'Avec \(X=x^2\) : \(2X^2-3X+1=0\), donc \(X=1\) ou \(X=\dfrac12\).', r'\(S=\left\{-1\,;-\dfrac{\sqrt2}2\,;\dfrac{\sqrt2}2\,;1\right\}\).'),
    Q(3, r'Résous \(x+y+z=3\), \(2x-y+z=4\), \(x+2y-z=-1\). <em>(2.5)</em>', r'\(L_2-L_1\) : \(x-2y=1\). \(L_1+L_3\) : \(2x+3y=2\).', r'Avec \(x=1+2y\) : \(2+7y=2\), donc \(y=0\), \(x=1\), \(z=2\). Solution \((1\,;0\,;2)\).'),
    Q(3, r'La longueur d\'un rectangle dépasse sa largeur de 3 cm, et son aire vaut 40 cm². Quelles sont ses dimensions ? <em>(2.3)</em>', r'\(x(x+3)=40\iff x^2+3x-40=0\). \(\Delta=169\), racines 5 et \(-8\).', r'Une largeur est positive : 5 cm sur 8 cm.'),
    Q(3, r'Une huile A contient 2 % d\'essence parfumée, une huile B en contient 12 %. Quels volumes mélanger pour obtenir 50 mL d\'huile à 6 % ? <em>(2.5)</em>', r'\(a+b=50\) et \(0{,}02a+0{,}12b=0{,}06\times50=3\).', r'Avec \(a=50-b\) : \(1+0{,}10\,b=3\), donc \(b=20\) mL et \(a=30\) mL.'),
]

data = {
    'subject': {'slug': 'maths', 'name': 'Mathématiques', 'position': 1},
    'chapter': {
        'slug': 'algebre-et-equations', 'number': 2, 'title': 'Algèbre et équations', 'cnamCode': None,
        'introHtml': "<p>Ce chapitre rassemble les outils de calcul qu'on utilise partout ensuite : transformer une expression, résoudre une équation ou une inéquation, et résoudre un système de plusieurs équations. Chaque problème concret, en chimie comme en informatique, finit par s'écrire avec eux.</p>",
    },
    'sections': sections,
    'exam': exam,
}

out = Path(__file__).resolve().parents[1] / '02-algebre-et-equations.json'
out.write_text(json.dumps(data, ensure_ascii=False, indent=2) + '\n', encoding='utf-8')
print(out, [(s['number'], len(s['examples']), len(s['questions'])) for s in sections], len(exam))
