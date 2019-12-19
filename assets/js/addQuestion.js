var $collectionHolder;

// setup an "add a tag" link
var $addQuestionButton = $('<button type="button" class="add_question_link btn btn-dark">Pridėti klausymą</button>');
var $newLinkLi = $('<li></li>').append($addQuestionButton);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of tags
    $collectionHolder = $('ul.questions');

    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addQuestionButton.on('click', function(e) {
        // add a new tag form (see next code block)
        addQuestionForm($collectionHolder, $newLinkLi);
    });
});

function addQuestionForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');
    // get the new index
    var index = $collectionHolder.data('index');
    var newForm = prototype;

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<li></li>').append(newForm);
    $newLinkLi.before($newFormLi);

    addQuestionFormDeleteLink($newFormLi);
}

function addQuestionFormDeleteLink($questionFormLi) {
    var $removeFormButton = $('<button class="btn btn-danger" type="button">Pašalinti</button>');
    $questionFormLi.append($removeFormButton);

    $removeFormButton.on('click', function(e) {
        // remove the li for the tag form
        $questionFormLi.remove();
    });
}