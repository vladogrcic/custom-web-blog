@extends('layouts.manage', ['title_text' => '<i class="fa fa-folder"></i>', 'item_type' => 'Media'])
@section('content')
    <div id="mainMedia">
        <img v-for="item in initFileProp['allFiles']" :src="'{{url('/')}}/storage/content/thumb-360/'+item" style="display: none;">
        <img src="{{asset('/images/folder.svg')}}" style="display: none;">
        <img src="{{asset('/images/folder-empty.svg')}}" style="display: none;">
        {{ csrf_field() }}
        <div class="media-manager-wrapper" style="position: relative;">
            <span class="crumbs breadcrumb has-succeeds-separator is-medium" style="display: none;" v-show="!isSiteLoading">
                <ul>
                    <li>
                        <span class="icon is-medium" @click="handleFileList('', 'crumbs', true)"><i class="fa fa-home"></i></span>
                    </li>
                    <li v-for="(item, index) in initFileProp['folderTree']" v-if="item">
                        <span class="m-l-15 m-r-15" @click="handleFileList(index, 'crumbs')" v-html="' '+item"></span>
                    </li>
                </ul>
            </span>
            <hr class="m-b-0">
            <div class="tools-bar m-b-15" style="display: none;" v-show="!isSiteLoading">
                <button class="button is-info is-medium" @click="isCardModalUploadActive = true">
                    <span class="icon"><i class="fa fa-upload"></i></span>
                </button>
                <b-modal :active.sync="isCardModalUploadActive" :width="1024" scroll="keep">
                    <div class="card">
                        <div class="card-content">
                            <b-field>
                                <b-upload v-model="files" multiple drag-drop name="upload" class="media-upload">
                                    <section class="section">
                                        <div class="content has-text-centered">
                                            <p>
                                                <b-icon icon="upload" size="is-large">
                                                </b-icon>
                                            </p>
                                            <p>Drop your files here or click to upload</p>
                                        </div>
                                    </section>
                                </b-upload>
                            </b-field>
                            <div class="upload-files">
                                <div v-for="(file, index) in files" :key="index" class="upload-file">
                                    <button class="delete is-medium" type="button" @click="deleteDropFile(index)">
                                    </button>
                                    <div class="upload-image" v-bind:style="{backgroundImage: 'url('+filesUrl[index]+')'}">
                                    </div>
                                    <div class="upload-title" v-html="slug(file.name)"></div>
                                </div>
                            </div>
                            <hr>
                            <input type="button" class="button" v-on:click="submitFiles()" value="Submit">
                        </div>
                    </div>
                </b-modal>
                <b-modal :active.sync="isCardModalPreviewActive" :width="1280" scroll="keep">
                    <image-preview :imgviewprop.sync = "imagePreviewProps">
                </image-preview>
                </b-modal>
                <button class="button is-success is-medium" @click="addNewFolderFunc('add')">
                    <span class="icon"><i class="fa fa-folder-open"></i></span>
                </button>
                @permission('update-media|delete-media')
                <div class="is-pulled-right">
                    @permission('delete-media')
                    <button class="button is-danger is-medium deleteItemBulk" v-if="editMode" @click="deleteItem('', 'deleteBulk', 'both')"/>
                    <i class="fa fa-trash" style="margin-top: -10px;margin-right: -20px;"></i><i class="fa fa-trash"></i></span>
                    </button>
                    @endpermission
                    @permission('update-media')
                    <button class="is-medium bulk-edit" v-bind:class="{'button is-link':!editMode, 'button editMode':editMode}" @click="editMode=!editMode">
                        <span class="icon"><i class="fa fa-edit"></i></span>
                    </button>
                </div>
                    @endpermission
                @endpermission
            </div>
            <div class="media-manager columns is-multiline" style="display: none;" v-show="!isSiteLoading">
                <div class="column is-one-quarter folder" v-for="(item, index) in initFileProp['folders']">

                    <div class="card" style="position:relative;">
                        <div class="card-image" @click.stop="handleFileList(index)">
                            <figure class="image">
                                <div v-if="initFileProp['folderProps'][index]['number'] || initFileProp['folderProps'][index]['files']" style="background-image: url({{asset('images/folder.svg') }});"></div>
                                <div v-else style="background-image: url({{asset('images/folder-empty.svg') }});"></div>
                                @permission('delete-media')
                                    <span v-show="!editMode" class="icon-round" @click.stop="deleteItem(index, 'delete', 'folder')">
                                        <span style="" class="icon has-text-danger fa fa-trash"></span>
                                    </span>
                                @endpermission
                            </figure>
                        </div>
                        <div class="card-content">
                            <div class="media">
                                <div class="media-content">
                                    <input v-show="editMode" type="checkbox" 
                                        style="width: 25px; height: 25px; position: absolute; top:10px; left:10px;"
                                        v-model="deleteBulkFolder[index]">
                                    <span class="icon title is-6"><i class="fa fa-folder"></i></span>
                                    <span class="subtitle is-6">@{{ initFileProp['folderProps'][index]['name'] }}</span>
                                    <div class="media-sub-content" style="">
                                        <div>
                                            <span class="icon title is-6"><i class="fa fa-folder-open"></i></span>
                                            <span class="subtitle is-6">@{{ initFileProp['folderProps'][index]['number']}}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="icon title is-6"><i class="fa fa-files-o"></i></span>
                                            <span class="subtitle is-6">@{{ initFileProp['folderProps'][index]['files']}}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="media-sub-content" style="">
                                        <div class="" style="">
                                            <span class="icon title is-6"><i class="fa fa-calendar"></i></span>
                                            <time class="subtitle is-7">@{{ initFileProp['folderProps'][index]['modified']['date']
                                                }}
                                            </time>
                                        </div>
                                        <div class="" style="">
                                            <span class="icon title is-6"><i class="fa fa-clock-o"></i></span>
                                            <time class="subtitle is-7">@{{ initFileProp['folderProps'][index]['modified']['time']
                                                }}
                                            </time>
                                        </div>
                                    </div>
                                    <div class="" style="height: 28px; display: inline-block;">
                                        <span class="icon title is-7"><i class="fa fa-hdd-o"></i></span>
                                        <span class="subtitle is-7">@{{ initFileProp['folderProps'][index]['size']
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="content">
                            </div>
                        </div>
                    </div>
                </div>
                <p class="" v-if="!initFileProp['folders'].length && !initFileProp['files'].length && !addNewFolder">
                    There are no images or folders.
                </p>
                <div id="new-folder" class="column is-one-quarter folder" v-if="addNewFolder">
                    <div class="card" style="position:relative;">
                        <div class="card-image">
                            <figure class="image">
                                <div style="background-image: url({{asset('images/folder.svg') }});"></div>
                                <span v-show="!editMode" class="icon-round" @click.stop="addNewFolderFunc">
                                    <span style="" class="icon has-text-danger fa fa-trash"></span>
                                </span>
                            </figure>
                        </div>
                        <div class="card-content">
                            <div class="media">
                                <div class="media-content">
                                    <div class="folder-create">
                                        <span class="title is-6">Folder Name</span>
                                        <input type="text" class="input" ref="focus" v-model="newFolder">
                                        <input type="button" class="button submit" value="Add Folder" @click="submitNewFolder">
                                    </div>
                                </div>
                            </div>
                            <div class="content">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-3 file" v-for="(item, index) in initFileProp['files']">{{--<div v-show="editMode" class="editing-foreground" @click="deleteBulk[index]=true"></div>--}}
                    <div class="card" v-if="item.length">
                        <div class="card-image">
                            <figure class="image">
                            <div @click.stop="launchImagePreview( index )" :style="{ backgroundImage:  'url({{url('/')}}/storage/content/thumb-360/'+initFileProp['fileProps'][index]['coreUrl']+')' }">
                                </div>
                                @permission('delete-media')
                                <span v-show="!editMode" class="icon-round" @click.stop="deleteItem(index, 'delete', 'file')">
                                    <span class="icon has-text-danger fa fa-trash"></span>
                                </span>
                                @endpermission
                                <span v-show="!editMode" class="icon-round" style="right:15px; color:blue;left: initial;">
                                    <b-tooltip
                                        :label="'{{URL::to('/')}}'+initFileProp['fileProps'][index]['url']"
                                        class="url-popup"
                                        type="is-info"
                                        size="is-large"
                                        multilined>
                                            <span class="icon has-text-info fa fa-link"></span>
                                    </b-tooltip>
                                </span>
                                
                            </figure>
                        </div>
                        <div class="card-content">
                            <div class="media">
                                <div class="media-content">
                                    <input v-show="editMode" type="checkbox" 
                                        style="width: 25px; height: 25px; position: absolute; top:10px; left:10px;"
                                        v-model="deleteBulk[index]">
                                    <div class="filename" style="">
                                        <div class="" style="">
                                            <span class="icon title is-7"><i class="fa fa-info"></i></span>
                                            <span class="subtitle is-7">@{{initFileProp['fileProps'][index]['name']}}</span><br>
                                        </div>
                                    </div>
                                    <div class="media-sub-content" style="">
                                        <div class="" style="">
                                            <span class="icon title is-7"><i class="fa fa-file"></i></span>
                                            <span class="subtitle is-7" style="">@{{initFileProp['fileProps'][index]['mimeType']}}</span>
                                        </div>
                                        <div class="" style="">
                                            <span class="icon title is-7"><i class="fa fa-eye"></i></span>
                                            <span class="subtitle is-7">@{{initFileProp['fileProps'][index]['resolution']['width']}}
                                                X @{{initFileProp['fileProps'][index]['resolution']['height']}}</span>
                                        </div>
                                    </div>
                                    <div class="media-sub-content" style="">
                                        <div class="" style="">
                                            <span class="icon title is-6"><i class="fa fa-calendar"></i></span>
                                            <time class="subtitle is-7">@{{initFileProp['fileProps'][index]['modified']['date']}}</time>
                                        </div>
                                        <div class="" style="">
                                            <span class="icon title is-6"><i class="fa fa-clock-o"></i></span>
                                            <time class="subtitle is-7">@{{initFileProp['fileProps'][index]['modified']['time']}}</time>
                                        </div>
                                    </div>
                                    <div class="media-sub-content">
                                        <div>
                                            <span class="icon title is-7"><i class="fa fa-hdd-o"></i></span>
                                            <span class="subtitle is-7">@{{initFileProp['fileProps'][index]['size']}}</span>
                                        </div>
                                        <div>
                                            <span class="icon title is-7"><i class="fa fa-wrench"></i></span>
                                            <span class="subtitle is-7">More</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <b-loading :is-full-page="isFullPage" :active.sync="isLoading" :can-cancel="false"></b-loading>
        </div>
    </div>
@endsection 
@section('scripts')
<script>
    var mainMedia = new Vue({
        el: '#mainMedia',
        data: {
            isSiteLoading: false,
            dropFiles: [],
            editMode: false,
            isCardModalUploadActive: false,
            isCardModalNewFolderActive: false,
            files: [],
            token: "{{ csrf_token() }}",
            initFileProp: {!!$initFileProp!!},
            isLoading: false,
            isFullPage: false,
            addNewFolder: false,
            newFolder: '',
            iconDeleteBackHover: null,
            deleteBulkFolder: [],
            deleteBulk: [],
            isCardModalPreviewActive: false,
            imagePreviewProps: {
                core: "",
                thumb: "",
                name: "",
                mimeType: "",
                resolution: {},
                modified: {},
                size: "",
                exif: {
                    DateTimeOriginal: {
                        date:'',
                        time:''
                    },
                    FocalLength: '',
                    FNumber: '',
                    ExposureTime: '',
                    Model: '',
                    Make: '',
                    COMPUTED: {
                        ApertureFNumber:'',
                    }
                }
            }
        },
        methods: {
            slug(input){
                return Slug(input);
            },
            openLoading(input) {
                this.isLoading ? this.isLoading = false : this.isLoading = true;
                input=='full' ? this.isFullPage = true : this.isFullPage = false;
                
            },
            launchImagePreview(index){
                Vue.set(
                    this.imagePreviewProps,
                    "thumb",
                    window.baseUrl + this.initFileProp["fileProps"][index]["thumbUrl"]
                );
                Vue.set(
                    this.imagePreviewProps,
                    "core",
                    this.initFileProp["fileProps"][index]["coreUrl"]
                );
                Vue.set(
                    this.imagePreviewProps,
                    "mimeType",
                    this.initFileProp["fileProps"][index]["mimeType"]
                );
                Vue.set(
                    this.imagePreviewProps,
                    "resolution",
                    this.initFileProp["fileProps"][index]["resolution"]
                );
                Vue.set(
                    this.imagePreviewProps,
                    "modified",
                    this.initFileProp["fileProps"][index]["modified"]
                );
                Vue.set(
                    this.imagePreviewProps,
                    "size",
                    this.initFileProp["fileProps"][index]["size"]
                );
                Vue.set(
                    this.imagePreviewProps,
                    "name",
                    this.initFileProp["fileProps"][index]["name"]
                );
                Vue.set(
                    this.imagePreviewProps,
                    "exif",
                    this.initFileProp["fileProps"][index]["exif"]
                );
                this.isCardModalPreviewActive = true;
            },
            addNewFolderFunc(param){
                if(param=='add') {
                    this.addNewFolder = true; 
                    var vm = this;
                    Vue.nextTick(function () {
                        vm.$refs.focus.focus();
                    });
                }
                else{ 
                    this.addNewFolder = false;
                    this.newFolder = '';
                }
            },
            submitNewFolder() {
                var vm = this, name = vm.initFileProp.folderTree.join('/');
                name.slice(-1)!='/'?name += '/':name;
                name += vm.slug(vm.newFolder);
                vm.openLoading();
                
                axios.post(siteUrl+'/manage/media', {
                    action: 'createFolder',
                    url: vm.initFileProp.folderTree.join('/'),
                    name: name,
                    _token: vm.token,
                    _method: "POST"
                }).then(function (response) {
                    vm.initFileProp = response.data;
                    vm.addNewFolder = false;
                    vm.editMode = false;
                    vm.newFolder = '';
                    vm.openLoading();
                }).catch(function (error) {
                    console.log(error);
                });
            },
            deleteItem(index, type, fileType) {
                @permission('delete-media')
                var vm = this, action, items=[], itemsFolder=[], name = vm.initFileProp.folderTree.join('/'), bulk;
                if(fileType=='file'){
                    if(type=='delete') {
                        action = 'delete';bulk=name.slice(7);
                        items.push(vm.initFileProp['files'][index]);
                    }
                    else {
                        action = 'delete';bulk=name.slice(7);   
                        for (var i = 0; i < vm.deleteBulk.length; i++) {
                            if(vm.deleteBulk[i]==true)
                                items.push(vm.initFileProp['files'][i]);
                        }
                    }
                }
                else{
                    if(fileType=='both'){
                        if(type=='delete') {
                            action = 'delete';bulk=name.slice(7);
                            items.push(vm.initFileProp['files'][index]);
                        }
                        else {
                            action = 'delete';bulk=name.slice(7);   
                            for (var i = 0; i < vm.deleteBulk.length; i++) {
                                if(vm.deleteBulk[i]==true)
                                    items.push(vm.initFileProp['files'][i]);
                            }
                        }
                    }
                    if(type=='delete') {
                        action = 'delete';bulk=name.slice(7);
                        itemsFolder.push(vm.initFileProp['folders'][index]);
                    }
                    else {
                        action = 'delete';bulk=name.slice(7);   
                        for (var i = 0; i < vm.deleteBulkFolder.length; i++) {
                            if(vm.deleteBulkFolder[i]==true){
                                name.slice(-1)!='/'?name += '/':name;
                                itemsFolder.push(vm.initFileProp['folders'][i]);
                            }
                                
                        }
                    }
                }
                if(!bulk) bulk = 'public';
                name.slice(-1)!='/'?name += '/':name;
                name += vm.newFolder;
                vm.openLoading();
                
                axios.post(siteUrl+'/manage/media/'+bulk, {
                    action: action,
                    url: vm.initFileProp.folderTree.join('/'),
                    items: items,
                    itemsFolder: itemsFolder,
                    name: name,
                    _token: vm.token,
                    _method: "DELETE"
                }).then(function (response) {
                    vm.initFileProp = response.data;
                    vm.addNewFolder = false;
                    vm.deleteBulk = [];
                    vm.deleteBulkFolder = [];
                    vm.newFolder = '';
                    vm.openLoading();
                }).catch(function (error) {
                    console.log(error);
                });
                @endpermission
            },
            handleFileList(index, type, root) {
                if(this.editMode) return;
                var location, array, vm = this;
                if (typeof type === 'undefined') type = '';
                if (type === 'crumbs') {
                    array = vm.initFileProp['folderTree'].slice(0, index + 1);
                    location = array.join('/');
                }
                else location = vm.initFileProp['folders'][index];
                if(root) location='/';
                vm.openLoading();
                
                axios.post(siteUrl+'/manage/media', {
                    url: location,
                    _token: vm.token,
                    _method: "GET"
                }).then(function (response) {
                    vm.initFileProp = response.data;
                    vm.addNewFolder = false;
                    vm.newFolder = '';
                    vm.openLoading();
                }).catch(function (error) {
                    console.log(error);
                });
            },
            deleteDropFile(index) {
                this.files.splice(index, 1)
            },
            /*
                Submits files to the server
            */
            submitFiles() {
                var formData = new FormData();
                var file;
                var vm = this;
                
                for (var i = 0; i < this.files.length; i++) {
                    file = this.files[i];
                    formData.append('files[' + i + ']', file, Slug(file.name));
                }
                formData.append('action', 'uploadFile');
                formData.append('currentFolder', '{{$folderUrl}}');

                formData.append('_token', vm.token);
                formData.append('_method', 'POST');

                formData.append('url', vm.initFileProp.folderTree.join('/'));
                vm.openLoading();
                axios.post(siteUrl+'/manage/media',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                ).then(function (response) {
                    console.log('SUCCESS!!');
                    var i = vm.files.length;
                    do {
                        vm.deleteDropFile(i - 1);
                        i--;
                    }
                    while (i);
                    vm.initFileProp = response.data;
                    vm.addNewFolder = false;
                    vm.newFolder = '';
                    vm.editMode = false;
                    vm.isCardModalUploadActive = false;
                    vm.openLoading('full');
                }).catch(function () {
                    console.log('FAILURE!!');
                });
            },
            /*
                Handles the uploading of files
            */
            handleFilesUpload() {
                var uploadedFiles = this.$refs.files.files;
                /*
                Adds the uploaded file to the files array
                */
                for (var i = 0; i < uploadedFiles.length; i++) {
                    this.files.push(uploadedFiles[i]);
                }
            },
            /*
                Removes a select file the user has uploaded
            */
            removeFile(key) {
                this.files.splice(key, 1);
            }
        },
        computed: {
            filesUrl: function () {
                var file, url = [];
                for (var i = 0; i < this.files.length; i++) {
                    file = this.files[i];
                    url.push(URL.createObjectURL(file));
                }
                return url;
            },
        },
        watch:{
            addNewFolder: function () { 
                Vue.nextTick(function () {
                    if(document.getElementById("new-folder"))
                        document.getElementById("new-folder").scrollIntoView();
                });
                // location.href = "#new-folder";
            },
            newFolder: _.debounce(function (input) {
                    this.newFolder = this.slug(input);
                }, 5000),
            isCardModalUploadActive: function (params) {
                if(params){
                    document.getElementById('admin-side-menu').style.position = 'absolute';
                    document.getElementById('admin-side-menu').style.zIndex = '0';
                }
                else {
                    document.getElementById('admin-side-menu').style.position = 'static';
                }
            }
        },
        mounted:function(){
            window.addEventListener("load", function(event) {
                this.isSiteLoading = false;
            });
        }   
    });
</script> 
@endsection