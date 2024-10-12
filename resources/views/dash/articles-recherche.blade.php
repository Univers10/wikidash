@extends('dash.base')
@section('content')
    <!-- Header -->
    {{-- <header class="bg-primary py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="text-white">Wikipedia Article Fetcher</h1>
            <nav>
                <a href="#" class="text-white text-decoration-none mx-3">Home</a>
                <a href="#" class="text-white text-decoration-none mx-3">About</a>
            </nav>
        </div>
    </header> --}}

    <!-- Search Bar -->
    <section class="container my-5">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="mb-4">Search Wikipedia Articles</h2>
                <input type="text" id="searchInput" class="form-control" placeholder="Enter a keyword...">
                <button id="searchBtn" class="btn btn-primary mt-3">Search</button>
            </div>
        </div>
    </section>

    <!-- Main Content: Articles -->
    <section class="container">
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
        document.getElementById('searchBtn').addEventListener('click', function() {
            const query = document.getElementById('searchInput').value;
            if (query) {
                fetchArticles(query);
            }
        });

        function fetchArticles(query) {
            const apiUrl =
                `https://fr.wikipedia.org/w/api.php?action=query&list=search&srsearch=${query}&format=json&origin=*`;

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
            container.innerHTML = ''; // Clear any previous content

            articles.forEach(article => {
                // Create the article card
                const col = document.createElement('div');
                col.classList.add('col-lg-4', 'mb-4');

                const card = document.createElement('div');
                card.classList.add('card');

                const cardBody = document.createElement('div');
                cardBody.classList.add('card-body');

                const cardTitle = document.createElement('h5');
                cardTitle.classList.add('card-title');
                cardTitle.textContent = article.title;

                const cardText = document.createElement('p');
                cardText.classList.add('card-text');
                cardText.innerHTML = article.snippet; // Snippet contains HTML, so use innerHTML

                const readMoreBtn = document.createElement('button');
                readMoreBtn.classList.add('btn', 'btn-primary');
                readMoreBtn.textContent = 'Read more';
                readMoreBtn.setAttribute('data-bs-toggle', 'modal');
                readMoreBtn.setAttribute('data-bs-target', '#articleModalTemplate');

                // Set up event listener to load the article content in the modal
                readMoreBtn.addEventListener('click', function() {
                    loadModalContent(article.title, article.pageid);
                });

                cardBody.appendChild(cardTitle);
                cardBody.appendChild(cardText);
                cardBody.appendChild(readMoreBtn);
                card.appendChild(cardBody);
                col.appendChild(card);

                container.appendChild(col);
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
