function onClickBtnLike(event){
    event.preventDefault();
    const url = this.href;
    const spanCount = this.querySelector('span.js-likes');
    const icon = this.querySelector('i');
    axios.get(url).then(function(response){
        spanCount.textContent = response.data.likes;
        if (icon.classList.contains('red')) {
            icon.classList.remove('red')
        } else {
            icon.classList.add('red')
        }
    }).catch(function (error) {
        if (error.response.status === 403) {
            window.alert("Vous ne pouvez pas aimer cette evenement si vous n'êtes pas connecté!");
        } else {
            window.alert("Une erreur s'est produite, Réessayer plus tard");
        }
    });
}
/*document.querySelectorAll('a.js-like-event').forEach(function (link) {
    link.addEventListener('click', onClickBtnLike);
});*/

$("a.js-like-event").on('click', onClickBtnLike);
