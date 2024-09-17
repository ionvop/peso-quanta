function GlobalEventListener(type, selector, callback) {
    document.addEventListener(type, (event) => {
        if (event.target.closest(selector)) {
            callback(event.target.closest(selector), event);
        }
    });
}

GlobalEventListener("click", ".-script__link", (element, event) => {
    if (event.shiftKey) {
        window.open(element.getAttribute("data-href"));
        return;
    }

    location.href = element.getAttribute("data-href");
});