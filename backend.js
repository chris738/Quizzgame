document.querySelectorAll('.answer').forEach(answer => {
    answer.addEventListener('click', function() {
        document.querySelectorAll('.answer').forEach(a => a.classList.remove('selected'));
        this.classList.add('selected');
    });
});

function getQuestions(QuestionID) {
    fetch(`backend.php?frageID=${QuestionID}`)
        .then(response => response.json())
        .then(data => {
            if (data.info) {
                document.getElementById('Question').textContent = data.info.fragen.Question;
                document.getElementById('green').textContent = data.info.fragen.AnswerGreen;
                document.getElementById('red').textContent = data.info.fragen.AnswerRed;
                document.getElementById('yellow').textContent = data.info.fragen.AnswerYellow;
                document.getElementById('blue').textContent = data.info.fragen.AnswerBlue;
            }
        })
        .catch(error => console.error('Fehler beim Abrufen der Daten in backend.js:', error));
}

function handleAnswerClick(id) {
    console.log('Button with id ' + id + ' clicked');
    // You can add your desired behavior here based on the clicked span's id.
    // For example:
    if(id === 'green') {
        alert('You selected Berlin!');
    } else if(id === 'red') {
        alert('You selected Madrid!');
    } else if(id === 'yellow') {
        alert('You selected Paris!');
    } else if(id === 'blue') {
        alert('You selected Rom!');
    }
}

getQuestions(1);
