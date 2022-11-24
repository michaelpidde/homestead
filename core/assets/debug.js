let toolbarToggle = document.querySelector('#homestead-debug-toggle');
toolbarToggle.onclick = function() {
    let content = document.querySelector('#homestead-debug-content');
    let showing = content.style.display == 'block';
    if(showing) {
        content.style.display = 'none';
        toolbarToggle.innerHTML = '&nwarr;';
    } else {
        content.style.display = 'block';
        toolbarToggle.innerHTML = '&searr;';
    }
}

let valueToggles = document.querySelectorAll('.identifier > .value-toggle');
valueToggles.forEach((toggle) => {
    toggle.onclick = function() {
        /* 
         * <div.property>
         *     <div.identifier>
         *         <div.value-toggle>
         *     </div>
         *     <div.value>
         * </div>
         */
        let content = toggle.parentElement.parentElement.querySelector('.value');
        let showing = content.style.display == 'inline-block';
        if(showing) {
            content.style.display = 'none';
            toggle.innerHTML = '+';
        } else {
            content.style.display = 'inline-block';
            toggle.innerHTML = '&ndash;';
        }
    }
});