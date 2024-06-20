document.addEventListener('DOMContentLoaded', () => {
    let noPosts = false;
    let cardCollection = document.getElementsByClassName('blog-card');
    let cardCollectionLength = cardCollection.length;

    const countCards = () => {
        let cardCount = 0;
        for(let i = 0; i < cardCollectionLength; i++) {
            const targetSpanInner = cardCollection[i].getElementsByTagName("span")[0].innerHTML;
            if (targetSpanInner.includes("No posts found")) {
                noPosts = true
            } else {
                cardCount++;
            }
        }
        return cardCount;
    }

    const paginationCalc = (collection, page_size, page_number) => {
        let array = [].slice.call(collection);
        return {
            array: array,
            slice: array.slice((page_number - 1) * page_size, page_number * page_size)
        }
    }

    const renderPaginationButtons = () => {
        for (let i = 0; i < cardCollectionLength; i++) {
            i = i++
            i = i.toString()
            const btn = document.createElement('button');
            btn.setAttribute('data-page', i);
            btn.innerHTML = i;
            document.getElementById('pagination');
        }
    }

    const drawPaginatedCards = (page) => {
        const paginationCalc = paginationCalc(cardCollection, 3, page);
        const cardArray = paginationCalc[0];
    }

    document.addEventListener('click', (e) => {
        if (e.target instanceof HTMLButtonElement) {
            const clickedBtn = e.target;
            const pageNumber = parseInt(clickedBtn.getAttribute('data-page'));
            const page = paginationCalc(cardCollection, 3, pageNumber);
            console.log(page);
        }
    });

    //TODO Render pagination buttons based on number of cards

    (async() => {
        if (countCards() > 0) {
            renderPaginationButtons();
        }
    })();
});