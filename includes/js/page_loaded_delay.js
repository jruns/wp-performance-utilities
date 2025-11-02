document.addEventListener( "DOMContentLoaded", () => {
    document.querySelectorAll("script[data-type=page_loaded_delay]").forEach( el => {
        setTimeout( function () {
                el.src = el.dataset.src
            },
            el.dataset.delay
        );
    } );
} );