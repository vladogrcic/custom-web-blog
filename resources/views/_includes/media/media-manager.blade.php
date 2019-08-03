@section('extra')
<div id="mainMedia">
    <input type="button" class="button is-info" value="Upload" @click="isCardModalUploadActive = true">
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
    <input type="button" class="button is-success" value="Add Folder" @click="addNewFolder = !addNewFolder">

    <div style="float:right;">
        <input class="button is-danger" type="button" value="Delete" @click="deleteItem('', 'deleteBulk', 'both')" />
        <div class="flipswitch" style="display:inline-block;">
            <input v-model="editable" type="checkbox" id="fs" class="flipswitch-cb" value="editing">
            <label for="fs" class="flipswitch-label">
                <div class="flipswitch-inner"></div>
                <div class="flipswitch-switch"></div>
            </label>
        </div>
        <label>Edit Mode</label>
    </div>

    <hr class="m-t-50">
    <div class="media-manager-wrapper" style="position: relative;">
        <span class="crumbs">
            <span @click="handleFileList(index, 'crumbs')" v-for="(item, index) in initFileProp['folderTree']" v-if="item" v-html="item+' / '">/</span>
        </span>
        <hr>
        <div class="media-manager columns is-multiline">
            <div class="column is-one-quarter folder" v-for="(item, index) in initFileProp['folders']">

                <div class="card" style="position:relative;">
                    <div class="card-image" @click.stop="handleFileList(index)">

                        <figure class="image">
                            <div v-if="initFileProp['folderProps'][index]['number'] || initFileProp['folderProps'][index]['files']" style="background-image: url({{asset('images/folder.svg') }});"></div>
                            <div v-else style="background-image: url({{asset('images/folder-empty.svg') }});"></div>
                            @can('delete-media')
                                <span v-show="!editable" class="icon-round" @click.stop="deleteItem(index, 'delete', 'folder')">
                                    <span style="" class="icon has-text-danger fa fa-trash"></span>
                                </span>
                            @endcan
                        </figure>

                    </div>
                    <div class="card-content">
                        <div class="media">
                            <div class="media-content">
                                <input v-show="editable" type="checkbox" style="width: 25px; height: 25px; position: absolute; z-index:99999; top:10px; left:10px;"
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
                                        <span class="subtitle is-6">@{{ initFileProp['folderProps'][index]['files'] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="media-sub-content" style="">
                                    <div class="" style="">
                                        <span class="icon title is-6"><i class="fa fa-calendar"></i></span>
                                        <time class="subtitle is-7">@{{ initFileProp['folderProps'][index]['modified']['day']
                                            }}
                                        </time>
                                    </div>
                                    <div class="" style="">
                                        <span class="icon title is-6"><i class="fa fa-clock-o"></i></span>
                                        <time class="subtitle is-7">@{{ initFileProp['folderProps'][index]['modified']['hour']
                                            }}
                                        </time>
                                    </div>
                                </div>


                                <div class="" style="height: 28px; display: inline-block;">
                                    <span class="icon title is-7"><i class="fa fa-hdd-o"></i></span>
                                    <span class="subtitle is-7">@{{ initFileProp['folderProps'][index]['size'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="content">
                        </div>
                    </div>

                </div>
            </div>
            <div id="new-folder" class="column is-one-quarter folder" v-if="addNewFolder">
                <div class="card" style="position:relative;">
                    <div class="card-image">

                        <figure class="image">
                            <div style="background-image: url({{asset('images/folder.svg') }});"></div>

                        </figure>

                    </div>
                    <div class="card-content">
                        <div class="media">
                            <div class="media-content">

                                <div class="folder-create">
                                    <span class="title is-6">Folder Name</span>

                                    <input type="text" class="input" v-model="newFolder">
                                    <input type="button" class="button submit" value="Add Folder" @click="submitNewFolder">
                                </div>

                            </div>
                        </div>
                        <div class="content">
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-3 file" v-for="(item, index) in initFileProp['files']">
                <div class="card">
                    <div class="card-image">
                        <figure class="image">
                            <div :style="{ backgroundImage:  'url('+initFileProp['fileProps'][index]['url']+')' }">
                            </div>
                            @permission('delete-media')
                                <span v-show="!editable" class="icon-round" @click.stop="deleteItem(index, 'delete', 'file')">
                                    <span class="icon has-text-danger fa fa-trash"></span>
                                </span>
                            @endpermission
                            <span v-show="!editable" class="icon-round" style="right:15px; color:blue;left: initial;">
                                <b-tooltip :label="'{{URL::to('/')}}'+initFileProp['fileProps'][index]['url']" type="is-info" multilined>
                                    <span class="icon has-text-info fa fa-link"></span>
                                </b-tooltip>
                            </span>
                        </figure>
                    </div>
                    <div class="card-content">
                        <div class="media">
                            <div class="media-content">
                                <input v-show="editable" type="checkbox" style="width: 25px; height: 25px; position: absolute; z-index:99999; top:10px; left:10px;"
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
                                        <time class="subtitle is-7">@{{initFileProp['fileProps'][index]['modified']['day']}}</time>
                                    </div>
                                    <div class="" style="">
                                        <span class="icon title is-6"><i class="fa fa-clock-o"></i></span>
                                        <time class="subtitle is-7">@{{initFileProp['fileProps'][index]['modified']['hour']}}</time>
                                    </div>
                                </div>

                                <div class="media-sub-content">
                                    <div>
                                        <span class="icon title is-7"><i class="fa fa-hdd-o"></i></span>
                                        <span class="subtitle is-7">@{{initFileProp['fileProps'][index]['size']}}</span>
                                    </div>
                                    <div>
                                        <span class="icon title is-7"><i class="fa fa-wrench"></i></span>
                                        <span class="subtitle is-7" style="color: blue; font-weight:500;">More</span>
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
<div>ghzgddf</div>
@endsection
@section('extrascripts')
<script>
var mainMedia = new Vue({
        el: '#mainMedia',
        data: {
            dropFiles: [],
            editable: false,
            isCardModalUploadActive: false,
            isCardModalNewFolderActive: false,
            files: [],
            token: "{{ csrf_token() }}",
            initFileProp: [],
            isLoading: false,
            isFullPage: false,
            addNewFolder: false,
            newFolder: '',
            iconDeleteBackHover: null,
            deleteBulkFolder: [],
            deleteBulk: [],
        },
        methods: {
            slug(input){
                return Slug(input);
            },
            openLoading(input) {
                this.isLoading ? this.isLoading = false : this.isLoading = true;
                input=='full' ? this.isFullPage = true : this.isFullPage = false;
                
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
                    vm.editable = false;
                    vm.newFolder = '';
                    vm.openLoading();
                }).catch(function (error) {
                    console.log(error);
                });
            },
            deleteItem(index, type, fileType) {
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
            },
            handleFileList(index, type) {
                if(this.editable) return;
                var location, array, vm = this;
                if (typeof type === 'undefined') type = '';
                if (type === 'crumbs') {
                    array = vm.initFileProp['folderTree'].slice(0, index + 1);
                    location = array.join('/');
                }
                else {
                    if(!Object.keys(vm.initFileProp).length === 0)
                        location = vm.initFileProp['folders'][index];
                    else location='public';
                };

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
                // formData.append('editMode', 'editing');
                formData.append('action', 'uploadFile');
                formData.append('currentFolder', '{{--$folderUrl--}}');
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
                    vm.editable = false;
                    vm.openLoading('full');
                }).catch(function () {
                    console.log('FAILURE!!');
                });
            },

            /*
                Handles the uploading of files
            */
            handleFilesUpload() {
                let uploadedFiles = this.$refs.files.files;

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
        created: function (params) {
            this.handleFileList(0);
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
            }, 5000)
        }   
    });
</script>
@endsection
