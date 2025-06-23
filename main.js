import u from '/node_modules/umbrellajs/umbrella.esm.js';
const selections = u('.selection');
const body = u('body');
const footer = u('footer');
selections.on('click', (e)=>{
    const selected = u(e.target);
    selections.removeClass('selected-glasses');
    setTimeout((e)=>{
        selected.addClass('selected-glasses');
        body.removeClass('light dark sepia');
        footer.removeClass('light dark sepia');
        body.addClass(selected.data('mode'));
        footer.addClass(selected.data('mode'));
    }, 100)
})