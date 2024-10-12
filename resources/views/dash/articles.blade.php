@extends('dash.base')
@section('content')
@section('page', 'Mes articles')
@section('search')
    <input type="text" id="searchInput" name="search" placeholder="Search">
    <span> <button id="searchBtn" class="btn btn-primary mt-3">Search</button></span>
    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
@endsection

<!-- Bloc1 -->
<div id="bloc1" class="mb-40">
    <div class="row gy-4">
        <!-- Les cartes dans le Bloc 1 -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card h-100 radius-12">
                <img src="assets/images/card-component/card-img1.png" class="card-img-top" alt="">
                <div class="card-body p-16">
                    <h5 class="card-title text-lg text-primary-light mb-6">Titre de l'article</h5>
                    <p class="card-text text-neutral-600">We quickly learn to fear and thus automatically avoid
                        potentially stressful situations of all kinds.</p>
                    <a href="javascript:void(0)"
                        class="btn text-primary-600 hover-text-primary px-0 py-10 d-inline-flex align-items-center gap-2">
                        Read More <iconify-icon icon="iconamoon:arrow-right-2" class="text-xl"></iconify-icon>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bloc2: Articles recherchés -->
<section id="bloc2" class="container" style="display: none;">
    <div class="row" id="articlesContainer">
        <!-- Articles from Wikipedia API will be inserted here dynamically -->
    </div>
</section>

<!-- Modal Template (hidden, will be cloned for each article) -->
<div class="modal fade" id="articleModalTemplate" tabindex="-1" aria-labelledby="articleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="articleModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Article content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript to interact with Wikipedia API -->
<script>
function createCard(title, description, imageUrl, linkUrl) {
    // Créer l'élément col pour la carte
    const col = document.createElement('div');
    col.classList.add('col-xxl-3', 'col-sm-6');

    // Créer la carte
    const card = document.createElement('div');
    card.classList.add('card', 'h-100', 'radius-12');

    // Ajouter l'image
    const img = document.createElement('img');
    img.src = imageUrl || 'assets/images/card-component/card-img1.png'; // Utiliser une image par défaut si aucune URL n'est fournie
    img.classList.add('card-img-top');
    img.alt = title;

    // Créer le corps de la carte
    const cardBody = document.createElement('div');
    cardBody.classList.add('card-body', 'p-16');

    // Titre de la carte
    const cardTitle = document.createElement('h5');
    cardTitle.classList.add('card-title', 'text-lg', 'text-primary-light', 'mb-6');
    cardTitle.textContent = title;

    // Texte de la carte
    const cardText = document.createElement('p');
    cardText.classList.add('card-text', 'text-neutral-600');
    cardText.innerHTML = description; // Utiliser innerHTML pour interpréter le HTML dans la description

    // Bouton "Read More"
    const readMoreBtn = document.createElement('a');
    readMoreBtn.href = linkUrl || 'javascript:void(0)';
    readMoreBtn.setAttribute('target', '_blank'); // Ouvrir dans un nouvel onglet
    readMoreBtn.classList.add('btn', 'text-primary-600', 'hover-text-primary', 'px-0', 'py-10', 'd-inline-flex', 'align-items-center', 'gap-2');
    readMoreBtn.textContent = 'Read More ';

    // Icône dans le bouton
    const icon = document.createElement('iconify-icon');
    icon.setAttribute('icon', 'iconamoon:arrow-right-2');
    icon.classList.add('text-xl');
    readMoreBtn.appendChild(icon);

    // Assembler le corps de la carte
    cardBody.appendChild(cardTitle);
    cardBody.appendChild(cardText);
    cardBody.appendChild(readMoreBtn);

    // Assembler la carte
    card.appendChild(img);
    card.appendChild(cardBody);

    // Ajouter la carte dans la colonne
    col.appendChild(card);

    return col;
}


    // Event listener pour la recherche
    document.getElementById('searchBtn').addEventListener('click', function() {
        const query = document.getElementById('searchInput').value;
        if (query) {
            // Cache le Bloc 1 et affiche le Bloc 2
            document.getElementById('bloc1').style.display = 'none';
            document.getElementById('bloc2').style.display = 'block';

            // Appelle la fonction pour rechercher les articles
            fetchArticles(query);
        }
    });

    function fetchArticles(query) {
        const apiUrl =
            `https://fr.wikipedia.org/w/api.php?action=query&list=search&srsearch=${query}&prop=pageimages&format=json&origin=*`;

        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                const articles = data.query.search;
                displayArticles(articles);
            })
            .catch(error => console.error('Error fetching Wikipedia data:', error));
    }

    function displayArticles(articles) {
        const container = document.getElementById('articlesContainer');
        container.innerHTML = ''; // Efface le contenu précédent

        articles.forEach(article => {
            // Requête API pour récupérer l'image de l'article
            const pageUrl =
                `https://fr.wikipedia.org/w/api.php?action=query&prop=pageimages&pageids=${article.pageid}&pithumbsize=300&format=json&origin=*`;

            fetch(pageUrl)
                .then(response => response.json())
                .then(pageData => {
                    const page = pageData.query.pages[article.pageid];
                    const imageUrl = page.thumbnail ? page.thumbnail.source :
                        'assets/images/card-component/card-img1.png'; // Image par défaut si aucune image trouvée

                    // Utiliser la fonction createCard pour créer une carte pour chaque article
                    const newCard = createCard(
                        article.title, // Titre de l'article
                        article.snippet, // Description (snippet) qui contient du HTML
                        imageUrl, // Image associée à l'article
                        `https://en.wikipedia.org/?curid=${article.pageid}` // Lien vers l'article complet sur Wikipedia
                    );

                    // Ajouter la carte au conteneur
                    container.appendChild(newCard);
                })
                .catch(error => console.error('Error fetching page image:', error));
        });
    }

    function loadModalContent(title, pageid) {
        const modal = document.getElementById('articleModalTemplate');
        const modalTitle = modal.querySelector('.modal-title');
        const modalBody = modal.querySelector('.modal-body');

        modalTitle.textContent = title;
        modalBody.innerHTML = 'Loading content...';

        const contentUrl =
            `https://fr.wikipedia.org/w/api.php?action=query&prop=extracts&pageids=${pageid}&exintro&format=json&origin=*`;

        fetch(contentUrl)
            .then(response => response.json())
            .then(data => {
                const page = data.query.pages[pageid];
                modalBody.innerHTML = page.extract || 'No additional content available.';
            })
            .catch(error => {
                modalBody.innerHTML = 'Error loading content.';
                console.error('Error fetching article content:', error);
            });
    }
</script>
@endsection
