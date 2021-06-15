function onClickBtnLike(event){
    event.preventDefault();
    const url = this.href;
    const spanCount = $('#js-likes');
    const icon = this.querySelector('i');
    const spanText = this.querySelector('span');
    axios.get(url).then(function(response){
        spanCount.text(response.data.likes);
        if (icon.classList.contains('red')) {
            icon.classList.remove('red');
            spanText.textContent = response.data.label;
        } else {
            icon.classList.add('red');
            spanText.textContent = response.data.label;
        }
    }).catch(function (error) {
        console.log(error);
        if (error.response.status === 403) {
            window.alert("Vous ne pouvez pas aimer un projet si vous n'êtes pas connecté!");
        } else {
            window.alert("Une erreur s'est produite, Réessayer plus tard");
        }
    });
}
document.querySelectorAll('a.js-like-project').forEach(function (link) {
    link.addEventListener('click', onClickBtnLike);
});