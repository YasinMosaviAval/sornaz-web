<?
$article_item = $data['article_item'][0] ?? [];
$article_categories = explode(',', $article_item['categories']);
$article_tags = explode(',', $article_item['title']) ?? [];
$settings = setIndexforDataArray($data['settings'], 'variable_name');
$categories = setIndexforDataArray($data['categories'], 'variable_name');
$tags = setIndexforDataArray($data['tags'], 'variable_name');

// dump($article_item);
// exit();
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="admin-content">
            <div class="header_ac">
                <h1 class="h1_ac"><?= translate($settings, 'edit_articles_page_title') ?></h1>
                <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
            </div>
            <div style="text-align: end; margin: 2rem;">
                <a href="<?=baseUrl()?>/article/articleDetails/<?= $article_item['post_id'] ?>" style="margin: 0 2rem;"><?= translate($settings, 'edit_articles_preview') ?></a>
                <a href="<?=baseUrl()?>/admin/showArticleList/all/posts.updated_at" class="btn-outline"><?= translate($settings, 'edit_articles_back_to_list') ?></a>
            </div>




            <form method="POST" action="<?=baseUrl()?>/admin/edit_article/<?= $article_item['post_id'] ?>" enctype="multipart/form-data">
                <input type="hidden" name="post_id" id="post_id" value="<?= $article_item['post_id'] ?>" />
                <input type="hidden" name="author_id" id="author_id" value="<?= $article_item['author_id'] ?>" />
                <!-- <input type="hidden" name="modified" id="modified" value="<?//= $article_item['modified'] ?>" /> -->
                <input type="hidden" name="cover" id="cover" value="<?= $article_item['cover'] ?>" />
                <input type="hidden" name="type" id="type" value="<?= $article_item['type'] ?>" />
                <input type="hidden" name="comment_count" id="comment_count" value="<?= $article_item['comment_count'] ?>" />
                <input type="hidden" name="name" id="name" value="<?= $article_item['name'] ?>" />
                <input type="hidden" name="pinged" id="pinged" value="<?= $article_item['pinged'] ?>" />
                <input type="hidden" name="guid" id="guid" value="<?= $article_item['guid'] ?>" />
                <input type="hidden" name="related_posts_id" id="related_posts_id" value="<?= $article_item['related_posts_id'] ?>" />

                <!-- <input type="hidden" name="title_fa" id="title_fa" value="<?//= $article_item['title_fa'] ?>" /> -->
                <!-- <input type="hidden" name="title_en" id="title_en" value="<?//= $article_item['title_en'] ?>" /> -->
                <!-- <input type="hidden" name="excerpt_fa" id="excerpt_fa" value="<?//= $article_item['excerpt_fa'] ?>" /> -->
                <!-- <input type="hidden" name="excerpt_en" id="excerpt_en" value="<?//= $article_item['excerpt_en'] ?>" /> -->

                <!-- <input style="display: none;" type="hidden" name="content_fa" id="content_fa" value="<?//= $article_item['content_fa'] ?>" />
                <input style="display: none;" type="hidden" name="content_en" id="content_en" value="<?//= $article_item['content_en'] ?>" /> -->

                <div>
                    <label for="title"><?= translate($settings, 'edit_articles_title') ?></label>
                    <input type="text" id="title" name="title" required placeholder="<?= translate($settings, 'edit_articles_title_placeholder') ?>" value="<?= translateStrings($article_item, 'title') ?>">
                </div>

                <div>
                    <label for="excerpt"><?= translate($settings, 'edit_articles_excerpt') ?></label>
                    <textarea id="excerpt" name="excerpt" rows="4" placeholder="<?= translate($settings, 'edit_articles_title_placeholder') ?>"><?= translateStrings($article_item, 'excerpt') ?></textarea>
                </div>

                <div>
                    <label for="categories"><?= translate($settings, 'edit_articles_categories') ?></label>
                    <ul id="categorychecklist" data-wp-lists="list:category" class="categorychecklist">
                        <? foreach($categories as $key => $value) { ?>
                            <li id='' class="">
                                <label class="">
                                    <?= translate($categories, $key) ?>
                                    <input type="checkbox" name="<?= $key ?>" id="<?= $key ?>" value="<?= $value['setting_id'] ?>" <?= strhas($article_item['categories'], $value['setting_id']) ? 'checked' : '' ?> />
                                </label>
                            </li>
                        <? } ?>
                    </ul>
                </div>
                <div>
                    <label for="tags"><?= translate($settings, 'edit_articles_tags') ?></label>
                    <ul id="tags_checklist" data-wp-lists="list:tag" class="categorychecklist">
                        <? foreach($tags as $key => $value) { ?>
                            <li id='' class="">
                                <label class="">
                                    <?= translate($tags, $key) ?>
                                    <input type="checkbox" name="<?= $key ?>" id="<?= $key ?>" value="<?= $value['setting_id'] ?>" <?= strhas($article_item['title'], $value['setting_id']) ? 'checked' : '' ?> />
                                </label>
                            </li>
                        <? } ?>
                    </ul>
                </div>
                <div>
                    <label for="keywords"><?= translate($settings, 'edit_articles_keywords') ?></label>
                    <input type="text" id="keywords" name="keywords" placeholder="<?= translate($settings, 'edit_articles_keywords_placeholder') ?>" value="<?//= $article_item['keywords'] ?>">
                    <small><?= translate($settings, 'edit_articles_keywords_note') ?></small>
                </div>

                <div>
                    <label for="featured_image"><?= translate($settings, 'edit_articles_cover') ?></label>
                    <input type="file" id="post_image" name="post_image" accept="image/*" value="" style="width: 500px;">
                    <img src="<?= get_article_origin_source($article_item['cover']) ?>" alt="<?= translateStrings($article_item, 'title') ?>" style="width: 500px;" >
                    <small><?= translate($settings, 'edit_articles_cover_note') ?></small>
                </div>

                <div>
                    <label for="content"><?= translate($settings, 'edit_articles_content') ?></label>
                    <textarea id="content" name="content" rows="20" required placeholder="<?= translate($settings, 'edit_articles_content_placeholder') ?>"><?= translateStrings($article_item, 'content') ?></textarea>
                </div>

                <div>
                    <label for="status"><?= translate($settings, 'articles_status_title') ?></label>
                    <select id="status" name="status">
                        <option value="draft" <?= $article_item['status'] === "draft" ? 'selected' : '' ?>><?= translate($settings, 'articles_status_draft') ?></option>
                        <option value="pending" <?= $article_item['status'] === "pending" ? 'selected' : '' ?>><?= translate($settings, 'articles_status_pending') ?></option>
                        <option value="private" <?= $article_item['status'] === "private" ? 'selected' : '' ?>><?= translate($settings, 'articles_status_private') ?></option>
                        <option value="publish" <?= $article_item['status'] === "publish" ? 'selected' : '' ?>><?= translate($settings, 'articles_status_publish') ?></option>
                    </select>

                    <!-- <span id="password-span"> -->
                        <label for="password"><?= translate($settings, 'edit_articles_password') ?></label>
                        <input type="text" name="password" id="password" value="<?= $article_item['password'] ?>"  maxlength="255" />
                    <!-- </span> -->
                </div>
                <div>
                    <label for="date"><?= translate($settings, 'edit_articles_publish_date') ?></label>
                    <input type="datetime-local" id="date" name="date" value="<?= $article_item['published_at'] ?>">
                </div>

                <a href="<?=baseUrl()?>/article/articleDetails/<?= $article_item['post_id'] ?>"><?= translate($settings, 'edit_articles_delete_button') ?></a>
                <a href="<?=baseUrl()?>/admin/edit_article/<?= $article_item['post_id'] ?>" class="btn-cancel"><?= translate($settings, 'edit_articles_discard_button') ?></a>
                <span id="sticky-span">
                    <input id="sticky" name="sticky" type="checkbox" value="sticky"  />
                    <label for="sticky" class="selectit"><?= translate($settings, 'edit_articles_pin_checkbox') ?></label>
                </span>
                <button type="submit"><?= translate($settings, 'edit_articles_publish_button') ?></button>


<br>
<br>
<br>
<br>
<br>

                <div class="editor-container">
  <div class="toolbar">
    <button onclick="format('undo')">Undo</button>
    <button onclick="format('redo')">Redo</button>

    <select onchange="setHeading(this.value)">
      <option value="">Normal</option>
      <option value="h1">H1</option>
      <option value="h2">H2</option>
      <option value="h3">H3</option>
      <option value="h4">H4</option>
      <option value="h5">H5</option>
      <option value="h6">H6</option>
    </select>

    <select onchange="setFontSize(this.value)">
      <option value="">Size</option>
      <option value="1">Small</option>
      <option value="3">Normal</option>
      <option value="5">Large</option>
      <option value="7">Huge</option>
    </select>

    <button onclick="format('bold')">Bold</button>
    <button onclick="format('italic')">Italic</button>
    <button onclick="format('underline')">Underline</button>

    <input type="color" onchange="setColor(this.value)">

    <button onclick="format('insertUnorderedList')">List</button>
    <button onclick="insertTable()">Table</button>

    <button onclick="addLink()">Link</button>
    <button onclick="triggerImageUpload()">Upload Image</button>

    <button onclick="format('justifyRight')">راست</button>
    <button onclick="format('justifyLeft')">چپ</button>

    <button onclick="toggleCodeView()">HTML</button>
  </div>

  <div id="editor" contenteditable="true"></div>
  <textarea id="codeView" class="code-view hidden"><?= translateStrings($article_item, 'content') ?></textarea>
  <input type="file" id="imageInput" class="hidden" accept="image/*">
</div>

            </form>


            
        </div>
    </div>
</div>



<script>
    const editor = document.getElementById("editor");
    const output = document.getElementById("output");
    const codeView = document.getElementById("codeView");
    const imageInput = document.getElementById("imageInput");
    let isCodeView = false;

    function format(cmd){document.execCommand(cmd,false,null);updateOutput();}
    function setHeading(tag){document.execCommand("formatBlock",false,tag);updateOutput();}
    function setFontSize(size){document.execCommand("fontSize",false,size);updateOutput();}
    function setColor(color){document.execCommand("foreColor",false,color);updateOutput();}

    function insertTable(){
    const r=prompt("ردیف:",2);const c=prompt("ستون:",2);
    if(!r||!c)return;
    let t="<table>";
    for(let i=0;i<r;i++){t+="<tr>";for(let j=0;j<c;j++){t+="<td>cell</td>";}t+="</tr>";}
    t+="</table>";
    document.execCommand("insertHTML",false,t);updateOutput();
    }

    function addLink(){const url=prompt("لینک:");if(url){document.execCommand("createLink",false,url);updateOutput();}}
    function triggerImageUpload(){imageInput.click();}

    imageInput.addEventListener("change",function(){
    const f=this.files[0];
    if(f){const r=new FileReader();r.onload=e=>{document.execCommand("insertImage",false,e.target.result);updateOutput();};r.readAsDataURL(f);} });

    editor.addEventListener("dragover",e=>{e.preventDefault();editor.classList.add("dragover");});
    editor.addEventListener("dragleave",()=>editor.classList.remove("dragover"));
    editor.addEventListener("drop",e=>{
    e.preventDefault();editor.classList.remove("dragover");
    const f=e.dataTransfer.files[0];
    if(f&&f.type.startsWith("image/")){
        const r=new FileReader();
        r.onload=ev=>{document.execCommand("insertImage",false,ev.target.result);updateOutput();};
        r.readAsDataURL(f);
    }
    });

    // CLICK IMAGE
    editor.addEventListener("click",e=>{if(e.target.tagName==="IMG")makeResizable(e.target);});

    function makeResizable(img){
    if(img.parentElement.classList.contains("img-wrapper")) return;

    const wrapper=document.createElement("div");
    wrapper.className="img-wrapper";
    img.classList.add("resizable");

    img.parentNode.insertBefore(wrapper,img);
    wrapper.appendChild(img);

    const corners=["br","bl","tr","tl"];

    corners.forEach(pos=>{
        const handle=document.createElement("div");
        handle.className="resizer "+pos;
        wrapper.appendChild(handle);

        let startX,startY,startW,startH;

        handle.addEventListener("mousedown",e=>{
        e.preventDefault();
        startX=e.clientX;
        startY=e.clientY;
        startW=img.offsetWidth;
        startH=img.offsetHeight;

        function resize(ev){
            let dx=ev.clientX-startX;
            let dy=ev.clientY-startY;

            if(pos==="br"){img.style.width=startW+dx+"px";}
            if(pos==="bl"){img.style.width=startW-dx+"px";}
            if(pos==="tr"){img.style.width=startW+dx+"px";}
            if(pos==="tl"){img.style.width=startW-dx+"px";}
        }

        function stop(){
            document.removeEventListener("mousemove",resize);
            document.removeEventListener("mouseup",stop);
            updateOutput();
        }

        document.addEventListener("mousemove",resize);
        document.addEventListener("mouseup",stop);
        });
    });
    }

    function toggleCodeView(){
    isCodeView=!isCodeView;
    if(isCodeView){codeView.value=editor.innerHTML;editor.classList.add("hidden");codeView.classList.remove("hidden");}
    else{editor.innerHTML=codeView.value;editor.classList.remove("hidden");codeView.classList.add("hidden");updateOutput();}
    }

    function updateOutput(){output.textContent=editor.innerHTML;}
    editor.addEventListener("input",updateOutput);
    updateOutput();
</script>

<?// View::renderPartial("/admin/edit-article/.php") ?>
<!-- 
INSERT INTO `sor_settings` (`page`, `variable_name`, `text_en`, `text_fa`) VALUES 
('edit_articles', 'edit_articles_content_placeholder', 'write here full content of your Article', 'محتوای کامل مقاله را اینجا بنویسید...'),
('edit_articles', 'edit_articles_password', 'Password', 'رمز عبور'),
('edit_articles', 'edit_articles_publish_date', 'Publish Date', 'تاریخ انتشار'),
('edit_articles', 'edit_articles_delete_button', 'Move to Trash', 'انتقال به زباله دان'),
('edit_articles', 'edit_articles_discard_button', 'Discard', 'لغو'),
('edit_articles', 'edit_articles_pin_checkbox', 'Pin to Top of Article List', 'سنجاق کردن در ابتدای لیست'),
('edit_articles', 'edit_articles_publish_button', 'Save And Publish', 'ذخیره و انتشار'),
('articles_status', 'articles_status_title', 'Publish Status', 'وضعیت انتشار'), 
('articles_status', 'articles_status_draft', 'Draft', 'پیش‌نویس'), 
('articles_status', 'articles_status_pending', 'Pending', 'در انتظار بررسی'), 
('articles_status', 'articles_status_private', 'Private Publish', 'انتشار خصوصی'), 
('articles_status', 'articles_status_publish', 'Publish', 'انتشار');
-->

