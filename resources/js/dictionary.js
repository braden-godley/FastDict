const $results = $("#results");
const $query = $("#query");

let timer = null;
let currentRun = 0;

function search() {
    if (timer !== null) {
        clearTimeout(timer);
    }

    timer = setTimeout(() => {
        runSearch();
        timer = null;
    }, 30);
}

async function runSearch() {
    const thisRun = currentRun + 1;
    currentRun = thisRun;

    const { words, definitions } = await $.get("/words?query=" + $query.val());

    // If a later API request finishes faster somehow, don't overwrite it!
    if (thisRun < currentRun) return;

    $results.empty();

    if (words.length === 0) {
        const $noResults = $("<p>")
            .addClass("px-4 py-2")
            .text("No results found!");
        $noResults.appendTo($results);
        return;
    }

    for (let i = 0; i < words.length; i++) {
        const word = words[i];

        const $result = $("<p>")
            .addClass("px-4 py-2")
            .text(word);
        $result.appendTo($results);

        if (i === 0) {
            const $container = $("<div>")
                .addClass("px-4 pb-4 border-l-2 border-gray-800")
                .appendTo($results)
                .hide();

            $.get("/definitions?query=" + word)
                .then(function({ definitions }) {
                    if (definitions === null || thisRun < currentRun) return;
                    for (const definition of definitions) {
                        const title = $("<p>").addClass("text-white px-4 pt-4")
                            .appendTo($container);

                        $("<span>")
                            .addClass("font-bold")
                            .text(definition.hwi.hw.replaceAll("*", ""))
                            .appendTo(title);

                        $("<span>")
                            .addClass("text-gray-500 ml-4 italic")
                            .text(definition.fl)
                            .appendTo(title);

                        // Show pronunciation
                        // if (definition.hwi.prs !== undefined) {
                        //     $("<span>")
                        //         .addClass("text-gray-500 ml-4")
                        //         .text("(" + definition.hwi.prs[0].mw + ")")
                        //         .appendTo(title);
                        // }

                        const $list = $("<ul>")
                            .addClass("list-disc pl-8")
                            .appendTo($container);

                        for (const shortDef of definition.shortdef) {
                            $("<li>")
                                .addClass("text-gray-400 mt-4")
                                .text(shortDef)
                                .appendTo($list);
                        }
                    }
                    $container.show();
                });
        }
    }
}

$query.on("keyup", search);

search();
