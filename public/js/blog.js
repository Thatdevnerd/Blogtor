document.addEventListener('DOMContentLoaded', () => {
    let cardCount = 0;
    let noPosts = false;
    let cardCollection = document.getElementsByClassName('blog-card');

    for(let i = 0; i < cardCollection.length; i++) {
        const targetSpan = cardCollection[i].getElementsByTagName("span")[0];
        if (cardCollection[i].getElementsByTagName("span")[0].innerHTML.includes("No posts found")) {
            noPosts = true
        }
        cardCount++;
    }
    console.log(cardCount, noPosts ? "No posts found" : "Posts found");
});