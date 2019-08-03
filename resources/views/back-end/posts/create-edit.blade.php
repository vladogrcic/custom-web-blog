@extends('layouts.manage', ['title_text' => $title_text, 'item_type' => $page])
@section('content')
        {{-- @if (!Route::is('posts.create'))
            <form action="{{route('posts.update', $post->id)}}" method="post">
        @else
            <form action="{{route('posts.store')}}" method="post">
        @endif --}}
            {{-- {{ csrf_field() }} --}}
            @if (!Route::is('posts.create'))
                {{method_field('PUT')}}
            @endif
            <b-loading :is-full-page="isFullPage" :active.sync="isLoading" :can-cancel="false"></b-loading>
            @if(!$locker)
            <div class="columns" style="display: none;" v-show="!isSiteLoading">
                <div class="column is-three-quarters-desktop is-three-quarters-tablet">
                    <div class="card m-b-10">
                        <div class="card-content">
                            <label class="label">Title</label>
                            <b-field>
                                <b-input type="text" placeholder="Post Title" size="is-large" v-model="title" name="title" @input="setSlug(slug)"></b-input>
                            </b-field>
                            <hr>
                            <label class="label">Permalink</label>
                            <slug-widget url="{{url('/')}}" :subdirectory="url" :slug="slug" :slug-exists="slugExists" :title="title" @copied="slugCopied" @slug-changed="updateSlug"></slug-widget>
                            <input type="hidden" v-model="slug" name="slug" value="hello" />
                            <hr>
                            <label class="label">Summary</label>
                            <b-field class="m-t-15">
                                <b-input maxlength="350" rows="4" type="textarea" placeholder="Compose your summary..." name="excerpt" value="{{$post->excerpt}}" v-model="excerpt">
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <b-field class="m-t-15">
                        <input type="hidden" name="content" v-model="editorData">
                        <ckeditor :editor="editor" v-model="editorData" :config="editorConfig"></ckeditor>
                    </b-field>
                    <div class="card card-widget tags-wrapper">
                        <div class="card-content">
                            <label class="label">
                                Tags
                            </label>
                            <div class="add-tags" v-for="(item, index) in tagsID">
                                <input type="hidden" class="12345" name="tagsNAME[]" :value="tags[index]">
                                <input type="hidden" class="12345" name="tags[]" :value="item">
                                @{{filteredTagsOBJ[index]}}
                            </div>
                            <b-taginput
                                @add="addedTag"
                                @remove="removeTag"
                                v-model="tags"
                                :data="filteredTags"
                                autocomplete
                                :allow-new="allowNew"
                                field="user.first_name"
                                icon="label"
                                placeholder="Add a tag" @typing="getFilteredTags">
                            </b-taginput>
                        </div>
                    </div>
                </div>
                <div class="column is-one-quarter-desktop is-narrow-tablet">
                    <div class="card card-widget">
                        <div class="author-widget widget-area">
                            <div class="selected-author">
                                <img width="50" height="50" :src="currentAuthorAvatar"/>
                                <div class="author">
                                    <h4 v-html="currentAuthorName">{{ $currentUser->name }}</h4>
                                </div>
                            </div>
                            @permission('all-posts')
                                <div class="otherUthor m-t-20 m-b-5">
                                    <b-checkbox 
                                        v-model="authorCustom"
                                        name="authorCustom"
                                        type="is-info">Choose Another Author:</b-checkbox>
                                    <div class="select full-width is-info m-t-10" v-if="authorCustom">
                                        <select name="author_id" v-model="author_id">
                                            <option :value="item.id" v-for="(item, index) in users" v-html="item.name" v-if="item.id!=currentAuthorID"></option>
                                        </select>
                                    </div>
                                </div>
                            @endpermission
                        </div>
                        @if(Route::is('posts.edit'))
                        <div class="post-status-widget widget-area">

                            <div class="status">
                                <div class="status-details">
                                        @if(strtotime($post->created_at_custom)!=false)
                                            <p><label class="label">Created: </label>{{$post->created_at_custom}}</p>
                                        @endif
                                        <p v-if="status && published_at"><label class="label">Published: </label><span v-html="published_at">{{$post->published_at}}</span></p>
                                </div>
                            </div>

                        </div>
                        @endif
                        @permission('publish-posts')
                    <div class="field m-t-15">
                        <div class="level">
                            <div class="level-item is-one-fifth has-text-centered"><label class="tag is-info">Draft</label></div>
                            <div class="level-item">
                                <div class="flipswitch">
                                    <input type="checkbox" name="status" id="fs" class="flipswitch-cb" value="publish" v-model="status">
                                    <label for="fs" class="flipswitch-label">
                                        <div class="flipswitch-inner"></div>
                                        <div class="flipswitch-switch"></div>
                                    </label>
                                </div>
                            </div>
                            <div class="level-item is-one-fifth has-text-centered"><label class="tag is-success">Publish</label></div>
                        </div>
                    </div>
                    @endpermission
                    {{-- <div class="button is-success" @click="updateForm"><i class="fa fa-send m-r-10"></i>Submit</div> --}}
                    
                    <div class="publish-buttons-widget widget-area">
                        <div class="secondary-action-button">
                            <button class="button is-success" :class="{ 'is-loading': sending, 'is-success': edited, 'is-hovered': !edited }" @click="updateForm()" :disabled="!edited">
                                <i class="fa fa-save"></i> 
                                <span>&nbsp;&nbsp;Save</span>
                            </button> 
                        </div>
                        <div class="primary-action-button">
                            <button class="button is-danger" @click="postLocker('force')"><i class="fa fa-times-circle"></i>&nbsp;&nbsp;Go Back</button>
                        </div>
                    </div>
                </div>
                <br/>
                <div class="card card-widget">
                    {{-- @if($languages) --}}
                        <div class="widget-area">
                            <label class="label">Language</label>
                            <div class="field">
                                <div class="control">
                                    <div class="select full-width is-info">
                                        <select name="language_id" v-model="language_id">
                                            <option value="{{ $post->language_id }}">{{ $post->language->name }}</option>
                                            @foreach ($languages as $language)
                                                @if ($language->id!=$post->language_id)
                                                    <option value="{{ $language->id }}">{{$language->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {{-- @endif --}}
                    <div class="widget-area">
                        <label class="label">Category</label>
                        <div class="field">
                            <div class="control">
                                <div style="width:100%">
                                    <div class="field" v-if="language_id==item.language_id" v-for="(item, index) in filteredCategories">
                                        <b-checkbox 
                                            v-model="categoryList"
                                            :native-value="item.id" 
                                            name="categories[]"
                                            type="is-info">
                                            @{{ item.name }}
                                        </b-checkbox>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="card card-widget">
                    <div class="widget-area">
                        <label class="label">Featured Image</label>
                        <div class="field" v-if="Object.keys(featuredImageUrl).length">
                            
                            <div class="control">
                                <figure class="image" @click="isCardModalPreviewActive = true" v-if="featuredImageUrl['core']">
                                    <input type="hidden" name="featured_image" v-model="featuredImageUrl.core">
                                    <img :src="siteUrl+'/storage/content/thumb-360/'+featuredImageUrl.core">
                                </figure>
                            </div>
                        </div>
                        <div class="field">
                            <div class ="p-t-15 p-l-15 p-r-15"  v-if="featuredImageUrl['name']">
                                <span class="icon"><i class="fa fa-info"></i></span>
                                <span class="subtitle is-7">@{{featuredImageUrl['name']}}</span><br>
                            
                                <span class="icon"><i class="fa fa-file"></i></span>
                                <span class="subtitle is-7">@{{featuredImageUrl['mimeType']}}</span><br>
                                
                                <span class="icon"><i class="fa fa-eye"></i></span>
                                <span class="subtitle is-7">@{{featuredImageUrl['resolution']['width']+ ' x ' +featuredImageUrl['resolution']['height']}}</span><br>
                            
                                <span class="icon"><i class="fa fa-calendar"></i></span>
                                <span class="subtitle is-7">@{{featuredImageUrl['modified']['date']+ ' at ' +featuredImageUrl['modified']['time']}}</span><br>
                            
                                <span class="icon"><i class="fa fa-hdd-o"></i></span>
                                <span class="subtitle is-7">@{{featuredImageUrl['size']}}</span><br>
                            </div>
                        </div>
                        <div class="field">
                            <div class="control featured-image-ctrl">
                                <input type="button" class="input" value="Pick Image" @click="isCardModalMediaLoadActive=true">
                                <input type="button" class="button is-danger" value="Remove Image" @click="removeFeaturedImg()">
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end of .column.is-one-quarter -->
            @endif
            <b-loading :is-full-page="isFullPage" :active.sync="isLoading" :can-cancel="false"></b-loading>
        </div>
    {{-- </form> --}}
    <b-modal :active.sync="isCardModalMediaLoadActive" :width="viewportWidth*0.8" scroll="keep">
        <media-manager :formurl="siteUrl+'/manage/media'" :imageurl="featuredImageUrl"></media-manager>
    </b-modal>
    <b-modal :active.sync="isCardModalMediaLoadActiveContent" :width="viewportWidth*0.8" scroll="keep">
        <media-manager :formurl="siteUrl+'/manage/media'" :imageurl="contentImageUrl"></media-manager>
    </b-modal>
    <b-modal :active.sync="isCardModalPreviewActive" :width="viewportWidth*0.8" scroll="keep">
        <image-preview :imgviewprop.sync = "featuredImageUrl">
        </image-preview>
    </b-modal>
    <b-modal :active.sync="errorLog" :can-cancel="['escape', 'outside']">
        <b-message :active.sync="errorLog" :title="errorLogTitle" type="is-danger" size="is-large" icon-pack="fa" icon-size="is-large" has-icon auto-close @close="redirectToPosts()">
            <p class="has-text-danger" style="margin-top: 6px;" v-html="errorLogMsg"></p>
        </b-message>
    </b-modal>
@endsection

@section('scripts')
    <script>
    var app = new Vue({
        el: '#app',
        data: {
            timePassed: {{ $timePassed }},
            viewportWidth: Math.max(document.documentElement.clientWidth, window.innerWidth || 0),
            siteUrl: siteUrl,
            locked_by: '{{ $locker }}',
            isFullPage: false,
            isLoading: false,
            id: {{$post->id}},
            formUrl: "{{!Route::is('posts.create')?route('posts.update', $post->id):route('posts.store')}}",
            method: "{{!Route::is('posts.create')?'PUT':'POST'}}",
            token: "{{ csrf_token() }}",
            isSiteLoading: false,
            languages: {!! json_encode($languages) !!},
            authorCustom: false,
            errorLog: false,
            errorLogMsg: '',
            errorLogTitle: '',
            edited: false,
            sending: false,
            author_id: 0,
            loggedUser: {{$loggedUser}},
            currentAuthorID: '{{$currentUser->id}}',
            currentAuthorName: '{{$currentUser->name}}',
            currentAuthorAvatar: '{{$currentUser->avatar}}',
            url: {!! json_encode($url) !!},
            title: '{{$post->title}}',
            slug: '{{$post->slug}}',
            excerpt: '{{$post->excerpt}}',
            published_at: '{{$post->published_at}}'!==''?'{{$post->published_at}}':false,
            slugExists: false,
            status: {{$post->status}},
            categories: {!! $post->categories !!},
            users: {!! $users !!},
            api_token: '{{Auth::user()->api_token}}',
            isCardModalMediaLoadActive: false,
            filteredTags: {!! json_encode($tagList) !!},
            filteredTagsDupli: {!! json_encode($tagList) !!},
            filteredTagsInit: {!! json_encode($tagListInit) !!},
            filteredTagsLangIDInit: {!! json_encode($tagListLangIDInit) !!},
            filteredTagsOBJ: {!! json_encode($tagListOBJ) !!},
            filteredCategories: {!! json_encode($categories) !!},
            isSelectOnly: false,
            tags: {!! json_encode($postTagList) !!},
            allowNew: true,
            categoryList: {!! json_encode($categoryList) !!},
            featuredImageUrl: {
                core: '{{$post->featured_image}}',
                thumb: '{{$featured_image_props["thumbUrl"]}}',
                name: '{{$featured_image_props["name"]}}',
                mimeType: '{{$featured_image_props["mimeType"]}}',
                resolution: {
                    height: '{{$featured_image_props["resolution"]["height"]}}',
                    width: '{{$featured_image_props["resolution"]["width"]}}'
                },
                modified: {
                    date: '{{$featured_image_props["modified"]["date"]}}',
                    time:'{{$featured_image_props["modified"]["time"]}}'
                },
                size: '{{$featured_image_props["size"]}}',
                exif: {!!json_encode($featured_image_props["exif"])!!},
            },
            featuredImageUrlInit: {
                core: '{{$post->featured_image}}',
                thumb: '{{$featured_image_props["thumbUrl"]}}',
                name: '{{$featured_image_props["name"]}}',
                mimeType: '{{$featured_image_props["mimeType"]}}',
                resolution: {
                    height: '{{$featured_image_props["resolution"]["height"]}}',
                    width: '{{$featured_image_props["resolution"]["width"]}}'
                },
                modified: {
                    date: '{{$featured_image_props["modified"]["date"]}}',
                    time:'{{$featured_image_props["modified"]["time"]}}'
                },
                size: '{{$featured_image_props["size"]}}',
                exif: {!!json_encode($featured_image_props["exif"])!!},
            },
            contentImageUrl: {
                core: '',
                thumb: '',
                name: '',
                mimeType: '',
                resolution: {
                    height: '',
                    width: ''
                },
                modified: {
                    date: '',
                    time:''
                },
                size: '',
                exif: {},
            },
            language_id: {{$language_id}},
            language_idInit: {{$language_id}},
            isCardModalPreviewActive: false,
            categoryListInit: {!! json_encode($categoryList) !!},
            page: "{{Route::is('posts.create')?'create':'edit'}}",
            isCardModalMediaLoadActiveContent: false,
            editor: FullEditor,
            // embedImageExecuted: embedImageExecuted,
            editorData: {!!$post->content!!},
            // plugins: [ Underline ],
            editorConfig: {
                // toolbar: [ 'bold', 'italic', 'Underline', 'strikethrough', 'code','subscript', 'superscript'  ]
                // toolbar: [ 'heading', '|', 'bold', 'italic', 'Underline', '|', 'pastetext' ],
                // plugins: [ CodePlugin ],
                toolbar: {
                    items: [
                        'heading',
                        '|',
                        'bold',
                        'italic',
                        'underline',

                        'imageApiEmbed',
                        'imageEmbed',
                        'mediaEmbed',

                        'strikethrough', 
                        'code',
                        'subscript', 
                        'superscript',
                        'Fontsize',
                        'Fontfamily',

                        // 'NumberedList', 
                        // 'BulletedList', 
                        'alignment',                     
                        
                        'link',
                        'bulletedList',
                        'numberedList',
                        // 'imageUpload',
                        'blockQuote',
                        'insertTable',
                        'undo',
                        'redo'
                    ]
                },
                image: {
                    toolbar: [ 
                        'imageTextAlternative', 
                        '|', 
                        'imageStyle:full', 
                        'imageStyle:alignLeft', 
                        'imageStyle:alignCenter', 
                        'imageStyle:alignRight',
                        // '|', 
                        // 'imageStyle:test' 
                    ],
                },
                table: {
                    contentToolbar: [
                        'tableColumn',
                        'tableRow',
                        'mergeTableCells'
                    ]
                },
                alignment: {
                    options: [ 'left', 'right', 'center', 'justify' ]
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                        { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                        { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]}
            },
        },
        methods: {
            redirectToPosts:function(){
                window.location.href = siteUrl+'/manage/posts';
            },
            setSlug: function(newVal) {
                if (newVal === "") return "";
                var slug = Slug(newVal);
                var vm = this;
                // if (this.api_token && slug) {
                //     axios.post(siteUrl+"/api/unique/slug/", {
                //             api_token: vm.api_token,
                //             slug: slug
                //         })
                //         .then(function(response) {
                //             vm.slugExists = false;
                //         })
                //         .catch(function(error) {
                //         console.log(error);
                //     });
                // }
            },
            updateSlug: function(val) {
                this.slug = val;
            },
            slugCopied: function(type, msg, val) {
                // notifications.toast(msg, {type: `is-${type}`});
            },
            addedTag: function (search_term) {
                var index = this.filteredTags.indexOf(search_term);
                if (index > -1) {
                    this.filteredTags.splice(index, 1);
                }
                index = this.filteredTagsDupli.indexOf(search_term);
                if (index > -1) {
                    this.filteredTagsDupli.splice(index, 1);
                }
            },
            removeTag: function (item) {
                this.filteredTags.indexOf(item)?this.filteredTags.push(item):this.filteredTags;
                this.filteredTagsDupli.indexOf(item)?this.filteredTagsDupli.push(item):this.filteredTagsDupli;
                console.log('hdfrgh');
            },
            getFilteredTags(text) {
                this.filteredTags = this.filteredTagsDupli;
                this.filteredTags = this.filteredTags.filter(function(option) {
                    return option
                        .toString()
                        .toLowerCase()
                        .indexOf(text.toLowerCase()) >= 0
                });
            },
            updateForm: function(){
                var vm = this;
                vm.postDataChanged('loading');
                axios.post(vm.formUrl, {
                    action: vm.formUrl,
                    _token: vm.token,
                    _method: vm.method,

                    authorCustom: vm.authorCustom,
                    author_id: vm.author_id,
                    title: vm.title,
                    slug: vm.slug,
                    excerpt: vm.excerpt,
                    content: vm.editorData,
                    status: vm.status,
                    categories: vm.categoryList,
                    tags: vm.tagsID,
                    tagsNAME: vm.tags,
                    featured_image: vm.featuredImageUrl.core,
                    language_id: vm.language_id,
                })
                .then(function(response) {
                    setTimeout(function () {
                        vm.postDataChanged('onresponse');
                        // vm.postData(response);
                    }, 1);
                    // vm.postDataChanged('onresponse');
                    if(vm.page == 'create'){
                        vm.postDataClear()
                    }
                    else{
                        vm.postData(response);
                    }
                    console.log(response);
                })
                .catch(function(error) {
                    if(error.response.status === 403){
                        if('locked_by' in error.response.data){
                            var locked_by = error.response.data.locked_by;
                            vm.alertCustomError(
                                'Post Is Being Edited', 
                                'Post being edited by '+locked_by,
                                function (params) {
                                    window.location.replace(vm.formUrl);
                                }
                            );
                        }
                        else{
                            vm.errorLog = true;
                            vm.errorLogMsg = 'You are not allowed to do this!<br>' + error.message;
                            vm.errorLogTitle = 'Permission Error';
                            vm.isLoading = false;
                        }
                    }
                    if(error.response.status === 404){
                        vm.errorLog = true;
                        vm.errorLogMsg = 'Item does not seem to exist in the database.<br>'+ error.message;
                        vm.errorLogTitle = 'Delete Error';
                        vm.isLoading = false;
                        if(typeof vm.items !== 'undefined')
                            vm.items =  error.response.data.items;
                        vm.id =  error.response.data.maxCol+1;
                        vm.idColMax =  error.response.data.maxCol+1;
                        vm.pageCount =  error.response.data.pageCount;
                        if(vm.currentPage > vm.pageCount) vm.currentPage = vm.pageCount;
                        vm.fieldEditMode = null;
                    }
                    if(error.response.status === 500){
                        vm.errorLog = true;
                        vm.errorLogMsg = 'Something went wrong.<br>' + error.message;
                        vm.errorLogTitle = 'Error';
                        vm.isLoading = false;
                        if(typeof vm.items !== 'undefined')
                            vm.posts = error.response.data;
                        vm.bulkEditing = [];
                        vm.fieldEditMode = null;
                    }
                    console.log(error);
                });
            },
            postDataChanged: function(params) {
                var vm = this;
                if(params === 'loading'){
                    vm.sending = true;
                    vm.isLoading = true;
                }
                else if(params === 'onresponse'){
                    vm.edited = false;
                    vm.sending = false;
                    vm.isLoading = false;
                }
                else{
                    vm.edited = true;
                    setTimeout(function () { 
                        vm.postLocker('editing');
                    }, 5000);
                }
            },
            postData: function(response) {
                var vm = this;
                vm.currentAuthorID = response.data.post.user.id;
                vm.currentAuthorName = response.data.post.user.name;
                vm.author_id = parseInt(response.data.post.author_id==vm.loggedUser?vm.users[0].id:vm.loggedUser);
                vm.published_at = response.data.post.published_at;
                // vm.title = response.data.post.title;
                // vm.slug = response.data.post.slug;
                // vm.excerpt = response.data.post.excerpt;
                // vm.editorData = response.data.post.content;
                vm.status = response.data.post.status;
                // vm.categoryList = response.data.post.categories;
                // vm.tags = response.data.postTagList;
                // vm.featuredImageUrl.core = response.data.post.featured_image;
                // vm.language_id = response.data.post.language_id;
                
            },
            postDataClear: function() {
                var vm = this;
                vm.author_id = 0;
                vm.title = '';
                vm.slug = '';
                vm.excerpt = '';
                vm.editorData = '';
                vm.status = 0;
                vm.categoryList = [];
                vm.tags = [];
                vm.featuredImageUrl = vm.featuredImageUrlInit;
                vm.language_id = 1;
                vm.authorCustom = false;
                
            },
            alertCustomError: function (title, msg, callback) {
                this.$dialog.alert({
                    title: title,
                    message: msg,
                    type: 'is-danger',
                    hasIcon: true,
                    icon: 'times-circle',
                    iconPack: 'fa',
                    canCancel: ['escape', 'outside'],
                    onConfirm:  callback,
                    onCancel:  callback
                    
                });
            },
            postLocker: function (input, callback) { 
                var vm = this;
                if(vm.page === 'edit'){
                    if(input === 'init'){
                        var locked_by = vm.locked_by;
                        if(locked_by && typeof callback == 'function'){
                            vm.isSiteLoading = true;
                            callback(locked_by);
                        }
                    }
                    else{
                        axios.post(siteUrl+"/api/posts/locker", {
                            api_token: vm.api_token,
                            action: input,
                            id: vm.id,
                        })
                        .then(function(response) {
                            if(input === 'force') {
                                vm.redirectToPosts();
                                console.log('dfghd');
                            }
                            var locked_by = response.data.locked_by;
                            if(locked_by && typeof callback == 'function'){
                                vm.isSiteLoading = true;
                                callback(locked_by);
                            }
                        })
                        .catch(function(error) {
                            var errorLogMsg = ''+error;
                            var errorLogTitle = 'Post Locking Error';
                            vm.alertCustomError(errorLogTitle, errorLogMsg);
                            console.log(error);
                        });
                    }
                    
                }
            },
            pageLostFocus: function (input) { 
                window.pageLostFocus = {};
                var vm = this;
                // if(input === 'init'){
                    document.addEventListener("focusin", function(){
                        clearTimeout(pageLostFocus);
                        vm.postLocker('editing', 
                        );
                    });
                // }
                // if(input === 'clear'){
                    document.addEventListener("focusout", function(){
                        pageLostFocus = setTimeout(() => {
                            console.log('Just printing something for testing.');
                            window.location.replace(vm.formUrl);
                        }, vm.timePassed * 1000);
                    });
                // }

            },
            removeFeaturedImg: function (input) {
                var vm = this;
                // vm.featuredImageUrl = Object.create(vm.emptyImageUrl);
                vm.featuredImageUrl = {
                    core: '',
                    thumb: '',
                    name: '',
                    mimeType: '',
                    resolution: {
                        height: '',
                        width: ''
                    },
                    modified: {
                        date: '',
                        time:''
                    },
                    size: '',
                    exif: {},
                };
            }
        },
        computed: {
            tagsID: function () {
                var arr=[];
                for(var i=0; i<this.tags.length; i++){
                    arr.push(this.filteredTagsOBJ[this.tags[i]]);
                }
                return arr;
            }
        },
        watch: {
            'featuredImageUrl.core': function (params) {
                this.postDataChanged();
                if(this.featuredImageUrl)
                    this.isCardModalMediaLoadActive = false;
            },
            'contentImageUrl.core': function (params) {
                // this.postDataChanged();
                if(this.contentImageUrl)
                    this.isCardModalMediaLoadActiveContent = false;
                if( this.contentImageUrl['core'] ) {
                    executeEmbedImage(siteUrl+origImageUrl+app.contentImageUrl['core']);
                }
            },
            
            postData: {
                handler: function (val, oldVal) {
                    this.postDataChanged();
                }, deep:true
            },
            authorCustom: function (params) {
                this.postDataChanged();
            },
            author_id: function (params) {
                this.postDataChanged();
            },
            title: function (params) {
                this.postDataChanged();
            },
            slug: function (params) {
                this.postDataChanged();
            },
            excerpt: function (params) {
                this.postDataChanged();
            },
            status: function (params) {
                this.postDataChanged();
            },
            categoryList: {
                handler: function (val, oldVal) {
                    this.postDataChanged();
                }, deep:true
            },
            tagsID: {
                handler: function (val, oldVal) {
                    this.postDataChanged();
                }, deep:true
            },
            tags: {
                handler: function (val, oldVal) {
                    this.postDataChanged();
                }, deep:true
            },
            language_id: function (params) {
                if(this.language_id!==0 && this.language_id){
                    this.categoryList = [];
                    if(this.language_id == this.language_idInit){
                        this.categoryList = this.categoryListInit;
                    }
                    this.filteredTags = [];
                    for (let i = 0; i < this.filteredTagsLangIDInit.length; i++) {
                        if(this.filteredTagsLangIDInit[i] == this.language_id){
                            this.filteredTags.push(this.filteredTagsInit[i]);
                        }
                    }
                    this.filteredTagsDupli = this.filteredTags;
                    this.postDataChanged();
                    this.tags = [];
                }
                var url_in = this.url.split('/');
                var language_id = parseInt(this.language_id);
                var url_out = this.languages[language_id-1].slug+'/'+url_in[1];
                this.url = url_out;
                //this.url = url_out.join('');
            },
            isCardModalPreviewActive: function (params) {
                if(params){
                    document.getElementById('admin-side-menu').style.position = 'absolute';
                    document.getElementById('admin-side-menu').style.zIndex = '0';
                }
                else {
                    document.getElementById('admin-side-menu').removeAttribute("style");
                }
            },
            isCardModalMediaLoadActive: function (params) {
                if(params){
                    document.getElementById('admin-side-menu').style.position = 'absolute';
                    document.getElementById('admin-side-menu').style.zIndex = '0';
                }
                else {
                    document.getElementById('admin-side-menu').removeAttribute("style");
                }
            },
            editorData: function (params) {
                this.postDataChanged();
            },
            
        },
        mounted:function(){
            var vm = this;
            window.addEventListener("load", function(event) {
                this.isSiteLoading = false;
                vm.postLocker('init',
                    function (locked_by) { 
                        vm.alertCustomError(
                            'Post Is Being Edited', 
                            'Post being edited by '+locked_by,
                            function (params) {
                                window.location.replace(vm.formUrl);
                            }
                        );
                    }
                );
                vm.pageLostFocus('');
            });
            window.embedImageExecuted = function(input){
                vm.isCardModalMediaLoadActiveContent = input;
            }
        }  
    });
    Array.prototype.remove = function(elem) {
        for (var i=this.length-1; i>=0; i--) {
            if (this[i] === elem) {
                this.splice(i, 1);
            }
        }
        return this;
    };
    </script>
@endsection
