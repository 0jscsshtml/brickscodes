function bcGutenbergFn(){setTimeout((()=>{const t=document.getElementById("toolbar-edit_with_bricks"),e=document.querySelector("#bricks-editor > .bricks-active .wp-editor-container > p > a");t?t.href=window.bricksData.builderEditLink+"&post_id="+window.bricksData.postId+"&post_type="+window.bricksData.currentScreen.post_type:e&&(e.href=window.bricksData.builderEditLink+"&post_id="+window.bricksData.postId+"&post_type="+window.bricksData.currentScreen.post_type)}),300)}document.addEventListener("DOMContentLoaded",(function(t){bcGutenbergFn()}));
/* 
document.addEventListener('DOMContentLoaded', function (e) {
	bcGutenbergFn()
});

function bcGutenbergFn() {	
	setTimeout(() => {		
		const editWithBrickBtn = document.getElementById('toolbar-edit_with_bricks');
		const editWithBrickBtnTemplate = document.querySelector('#bricks-editor > .bricks-active .wp-editor-container > p > a');
		if ( editWithBrickBtn ) {
			editWithBrickBtn.href = window.bricksData.builderEditLink + '&post_id=' + window.bricksData.postId + '&post_type=' + window.bricksData.currentScreen.post_type;
		} else if (editWithBrickBtnTemplate) {
			editWithBrickBtnTemplate.href = window.bricksData.builderEditLink + '&post_id=' + window.bricksData.postId + '&post_type=' + window.bricksData.currentScreen.post_type;
		}
	}, 300);
}
*/