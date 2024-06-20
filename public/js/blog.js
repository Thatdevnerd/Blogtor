document.addEventListener('DOMContentLoaded', () => {
    let cardCount = 0;
    let cardCollection = document.getElementsByClassName('blog-card');

    for(let i = 0; i < cardCollection.length; i++) {
        console.log(cardCollection[i]);
        cardCount++;
    }

    console.log(cardCount);
});