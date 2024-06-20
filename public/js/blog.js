document.addEventListener('DOMContentLoaded', () => {
    let cardCount = 0;
    let noPosts = false;
    let cardCollection = document.getElementsByClassName('blog-card');
    let cardCollectionLength = cardCollection.length;

    const countCards = () => {
        for(let i = 0; i < cardCollectionLength; i++) {
            const targetSpanInner = cardCollection[i].getElementsByTagName("span")[0].innerHTML;
            if (targetSpanInner.includes("No posts found")) {
                noPosts = true
            } else {
                cardCount++;
            }
        }
    }

    const cardsOnEnterAnimation = () => {
        for(let i = 0; i < cardCollectionLength; i++) {
            cardCollection[i].style.animation = `fadeIn 0.5s ${i * 0.1}s forwards`;
        }
        setTimeout((() => {
            for(let i = 0; i < cardCollectionLength; i++) {
                cardCollection[i].style.animation = 'none';
            }
        }, 3 * 1000));
    }

    const preventInitialCardLoad = () => {
        if (noPosts) {
            cardCollection[0].style.display = 'none';
        }
    }

    (async() => {
        countCards();
        cardsOnEnterAnimation();
        preventInitialCardLoad();
    })();

    console.log(cardCount)
});