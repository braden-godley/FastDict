const $results = $("#results");
const $query = $("#query");

let timer = null;

function search() {
    if (timer !== null) {
        clearTimeout(timer);
    }

    timer = setTimeout(() => {
        runSearch();
        timer = null;
    }, 200);
}

async function runSearch() {

    const { words, definitions } = await $.get("/words?query=" + $query.val());

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

        if (i === 0 && definitions.length > 0) {
            const $container = $("<div>")
                .addClass("px-4 pb-4 border-l-2 border-gray-500")
                .appendTo($results);

            for (const definition of definitions) {
                const title = $("<p>").addClass("text-white px-4 pt-4")
                    .appendTo($container);

                $("<span>")
                    .addClass("font-bold")
                    .text(definition.hwi.hw.replaceAll("*", ""))
                    .appendTo(title);

                $("<span>")
                    .addClass("text-gray-400 ml-4")
                    .text(definition.fl)
                    .appendTo(title);

                if (definition.hwi.prs !== undefined) {
                    $("<span>")
                        .addClass("text-gray-400 ml-4")
                        .text(definition.hwi.prs[0].mw)
                        .appendTo(title);
                }

                const $list = $("<ul>")
                    .addClass("list-disc pl-8")
                    .appendTo($container);
                for (const shortDef of definition.shortdef) {
                    $("<li>")
                        .addClass("text-white mt-4")
                        .text(shortDef)
                        .appendTo($list);
                }
            }
        } else {
            const $result = $("<p>")
                .addClass("px-4 py-2")
                .text(word.word);
            $result.appendTo($results);
        }
    }
}

$query.on("keyup", search);

search();
