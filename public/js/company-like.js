$("a.js-like-company").on('click', function(event){
    event.stopPropagation();
    event.preventDefault();
    const url = this.href;
    const spanCount = this.querySelector('span.js-likes');
    const icon = this.querySelector('i');
    const spanText = this.querySelector('span.js-text');
    axios.get(url).then(function(response){
        spanCount.textContent = response.data.likes;
        spanText.textContent = response.data.label;
        if (icon.classList.contains('red')) {
            icon.classList.remove('red')
        } else {
            icon.classList.add('red')
        }
    }).catch(function (error) {
        if (error.response.status === 403) {
            $('#modalInformationLike').modal('show');
        } else {
            window.alert("Une erreur s'est produite, Réessayer plus tard");
        }
    });
});