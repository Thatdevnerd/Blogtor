document.addEventListener('DOMContentLoaded', () => {
    const DEFAULT_PAGE = 1;
    const PAGE_SIZE = 3;

    let noPosts = false;

    let cardCollection = document.getElementsByClassName('blog-card');
    let cardCollectionLength = cardCollection.length;
    let cardParent = document.getElementsByClassName("blog-card-wrapper")[0];

    let cardObj = [{}];

    const countCards = () => {
        let cardCount = 0;
        for(let i = 0; i < cardCollectionLength; i++) {
            const targetSpanInner = cardCollection[i].getElementsByTagName("span")[0].innerHTML;
            if (targetSpanInner.includes("No posts found")) {
                noPosts = true;
            } else {
                cardCount++;
            }
        }
        return cardCount;
    }

    const paginationCalc = (collection, pageSize, pageNumber) => {
        const startIndex = (pageNumber - 1) * pageSize;
        const endIndex = pageNumber * pageSize;
        return collection.slice(startIndex, endIndex);
    };

    const renderPaginatedCards = (page, obj) => {
        cardParent.innerHTML = '';  // Clear existing cards
        const paginatedCards = paginationCalc(cardCollection, PAGE_SIZE, page);
        paginatedCards.forEach(card => {
            cardParent.appendChild(card);
        });
    }

    const renderPaginationButtons = () => {
        for (let i = 0; i < cardCollectionLength; i++) {
            i = i++;
            const btn = document.createElement('button');
            btn.setAttribute('data-page', i.toString());
            btn.innerHTML = i.toString();
            document.getElementById('pagination');
        }
    }

    document.addEventListener('click', (e) => {
        const pageNumber = parseInt(e.target.getAttribute('data-page'));
        renderPaginatedCards(pageNumber);
    });

    /**
     * IIFE to render paginated cards and pagination buttons
     */
    (async() => {
        const cardCount = countCards();
        if (cardCount > 0) {
            const numPages = Math.ceil(cardCount / PAGE_SIZE);
            renderPaginatedCards(DEFAULT_PAGE);
            renderPaginationButtons(numPages);
        }
    })();
});