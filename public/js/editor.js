/**
 * Transforms a string into a valid slug
 * 
 * @param {string} str 
 * @returns 
 */
function slugify(str) {
    str = str.trim();
    str = str.toLowerCase();
    str = str.replace(/[\d]+/g, '');
    str = str.trim();
    str = str.replace(/[\s_-]+/g, '-');
    str = str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    str = str.replace(/[^\w\s-]/g, '');


    return str;
}
/*
* Create a button for an action in a form.
* 
* @returns HTMLButtonElement
*/
function createButton() {
    

    return button;
}

/**
 * Initiate the slug generation action event.
 */
function slugAction() {
    const slugInput = document.querySelector('#slug');

    if (!slugInput) {
        return;
    }

    const parent = document.createElement('div');
    parent.classList = "input-group";
    slugInput.before(parent);

    const button = document.createElement('button');
    button.classList = "input-group-text";
    button.innerHTML = '<i class="fas fa-sync-alt"></i>';
    button.title = "Générer";
    button.type = "button";

    parent.appendChild(slugInput);
    parent.appendChild(button);
    slugInput.style.width = 'calc(100% - 50px)';

    button.addEventListener('click', (e) => {
        e.preventDefault();
        const title = document.querySelector('#title');
        if (title) {
            const slug = slugify(title.value);
            slugInput.value = slug;
        }
    });
}

slugAction();

/**
 * Enable the editor
 */
const textarea = document.querySelector('#content');
const toolbar = 'bold,italic,underline,strike|left,center,right,justify|size,color,font,removeformat|cut,copy,paste,pastetext|bulletlist,orderedlist|table,code,quote|horizontalrule,image,link,unlink|print,maximize,source';
sceditor.create(textarea, {
    format: 'bbcode',
    icons: 'monocons',
    style: '/sceditor/themes/content/default.min.css',
    toolbar: toolbar,
    emoticons: {},
    width: '100%',
    emoticonsEnabled: false,
    removeEmptyTags: true
});


