import u from '/umbrella.esm.js';
const selections = u('.selection');
const seeMores = u('.see-more');
const seeLesses = u('.see-less');
const body = u('body');
const footer = u('footer');
const closeButtons = u('.overlay-close-button');
const privacyPolicyLink = u('#privacy-policy-link');
const privacyPolicy = u('#privacy-policy');

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
seeMores.on('click', (e)=>{
    u(e.target).parent('.description-text').find('.expanded-description').removeClass('hidden');
    u(e.target).parent('.description-text').find('.see-less').removeClass('hidden');
    u(e.target).addClass('hidden');
});
seeLesses.on('click', (e)=>{
    u(e.target).parent('.description-text').find('.expanded-description').addClass('hidden');
    u(e.target).parent('.description-text').find('.see-more').removeClass('hidden');
    u(e.target).addClass('hidden');
});
closeButtons.on('click', (e)=>{
    const parentDiv = u(e.target).parent('div').parent('div');
    parentDiv.addClass('fade-closed');
    setTimeout(()=>{
        parentDiv.addClass('hidden');
        parentDiv.removeClass('fade-closed');
    }, 1000)
})
privacyPolicyLink.on('click', (e)=>{
    privacyPolicy.addClass('fade-open');
    privacyPolicy.removeClass('hidden');
    setTimeout(()=>{
        privacyPolicy.removeClass('fade-open');
    }, 1000)
})