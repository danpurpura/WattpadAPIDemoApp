$(document).ready(function() {
    var storiesToLoad = 0;

    // display the loading section
    var loading = $('#Loading');
    loading.append('<div>Loading categories...</div>');
    loading.show();

    // disable async
    $.ajaxSetup({async: false});

    // load our categories
    jQuery.getJSON(
        'ajax.php',
        'categories',
        function(data) {
            storiesToLoad = data.length;
            for(var id in data) {
                // load our story stats
                loading.append('<div>Loading statistics for category: ' + data[id].descr + '...</div>');
                createCategory(data[id]);
                jQuery.getJSON(
                    'ajax.php',
                    'stories&id=' + data[id].id,
                    function(storyData) {
                        appendResults(storyData);
                        storiesToLoad--;
                        if (storiesToLoad == 0) {
                            loading.hide();
                            $('#Results').show();
                        }
                    }
                )
            }
        }
    )
});

function createCategory(category) {
    $('#Results').append(
        '<div class="category" id="Category' + category.id + '">' + 
            '<div class="header">' +
                '<div>' + category.descr + '</div>' +
                '<div>Total</div>' +
                '<div>Min</div>' +
                '<div>Max</div>' +
                '<div>Avg</div>' +
                '<div>Mean</div>' +
            '</div>' +
        '</div>'
    ); 
}
function appendResults(storyData) {
    var category = $('#Category' + storyData.id);

    appendSection(category, 'Reads', storyData.stats.reads);
    appendSection(category, 'Votes', storyData.stats.votes);
    appendSection(category, 'Comments', storyData.stats.comments);
    appendSection(category, 'Pages', storyData.stats.pages);
    appendSection(category, 'Parts', storyData.stats.parts);
}

function appendSection(category, title, data) {
    category.append(
        '<div class="result">' +
            '<div>' + title + '</div>' +
            '<div>' + data.total + '</div>' +
            '<div>' + data.min + '</div>' +
            '<div>' + data.max + '</div>' +
            '<div>' + data.avg + '</div>' +
            '<div>' + data.mean + '</div>' +
        '</div>'
    );
}