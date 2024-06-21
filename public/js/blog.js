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

    const renderPaginatedCards = (page) => {
        const calc = paginationCalc(cardCollection, 3, page);
        const cardArray = calc.array;

        for (let i = 0; i < cardArray.length; i++) {
            const card = cardArray[i];
        }
    }

    document.addEventListener('click', (e) => {
        const clickedBtn = e.target;
        const pageNumber = parseInt(clickedBtn.getAttribute('data-page'));
        const page = paginationCalc(cardCollection, 3, pageNumber)


        if (countCards() >= 3) {
            const bodyRect = document.body.getBoundingClientRect()
            const elemRect = page.array[0].getBoundingClientRect();

            for(let i = 0; i < page.array.length; i++) {
                cardObj.push({
                    [1]: {
                        [i]: {
                            element: page.array[i],
                            top: elemRect.top - bodyRect.top,
                            left: elemRect.left - bodyRect.left,
                            width: page.array[i].offsetWidth,
                            height: page.array[i].offsetHeight
                        }
                    }
                })
                console.log('loading in card', page.array[i], 'card obj', cardObj);
            }

            let cardToRender = document.createElement('div');
            console.log(cardObj[1][1]);
            cardParent.append(cardObj[1][1][0].element)
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