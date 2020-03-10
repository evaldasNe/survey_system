var $collectionHolder;

var $addAnswerOptionButton = $('<button type="button" class="add_answer_option_link btn btn-dark">Add answer option</button>');
var $newLinkLi = $('<li></li>').append($addAnswerOptionButton);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of tags
    $collectionHolder = $('ul.answers');

    $collectionHolder.append($newLinkLi);

    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addAnswerOptionButton.on('click', function(e) {
        // add a new tag form (see next code block)
        addAnswerOptionForm($collectionHolder, $newLinkLi);
    });
});

function addAnswerOptionForm($collectionHolder, $newLinkLi) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var newForm = prototype;

    newForm = newForm.replace(/__name__/g, index);
    $collectionHolder.data('index', index + 1);

    var $newFormLi = $('<li></li>').append(newForm);
    $newLinkLi.before($newFormLi);

    addAnswerOptionFormDeleteLink($newFormLi);
}

function addAnswerOptionFormDeleteLink($answerOptionFormLi) {
    var $removeFormButton = $('<button class="btn btn-danger" type="button">Delete</button>');
    $answerOptionFormLi.append($removeFormButton);

    $removeFormButton.on('click', function(e) {
        // remove the li for the tag form
        $answerOptionFormLi.remove();
    });
}