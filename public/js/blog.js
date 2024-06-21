document.addEventListener('DOMContentLoaded', () => {
    let noPosts = false;
    let cardCollection = document.getElementsByClassName('blog-card');
    let cardParent = document.getElementsByClassName("blog-card-wrapper")[0]
    let cardCollectionLength = cardCollection.length;

    const DEFAULT_PAGE = 1;

    let cardHistoryObj = [{}];
    let cardObj = [{}];

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
            i = i++;
            const btn = document.createElement('button');
            btn.setAttribute('data-page', i.toString());
            btn.innerHTML = i.toString();
            document.getElementById('pagination');
        }
    }

    const renderPaginatedCards = (page, obj) => {
        const calc = paginationCalc(cardCollection, 3, page);
        let cardToRender = document.createElement('div');
        cardToRender.append(obj[page][1][0].element);
    }

    document.addEventListener('click', (e) => {
        const clickedBtn = e.target;
        const pageNumber = parseInt(clickedBtn.getAttribute('data-page'));
        const page = paginationCalc(cardCollection, 3, pageNumber)

        if (countCards() >= 3) {
            for(let i = 0; i < page.array.length; i++) {
                cardObj.push({
                    [1]: { //this mimics the designated page number
                        [i]: {
                            element: page.array[i],
                        }
                    }
                })
                console.log('loading in card', page.array[i], 'card obj', cardObj);
            }
            renderPaginatedCards(1, cardObj);
        }
    });

    /**
     * IIFE to render paginated cards and pagination buttons
     */
    (async() => {
        if (countCards() > 0) {
            renderPaginatedCards(DEFAULT_PAGE);
            renderPaginationButtons();
        }
    })();
});