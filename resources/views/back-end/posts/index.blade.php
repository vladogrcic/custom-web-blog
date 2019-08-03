@extends('layouts.manage', ['title_text' => $title_text, 'item_type' => 'Posts', 
'customButton' => '<a href="'.route('posts.create').'" class="button is-warning is-pulled-right"><i class="fa fa-file-o m-r-10"></i>Create Post</a>'
])
@section('content')

    <div class="card p-b-25 m-b-25">
        <div style="display: none;" v-show="!isSiteLoading">
            <div class="bulk-edit-tools tools-bar" style="" v-if="posts.length">

                <b-checkbox v-model="column.id"
                type="is-info">
                    ID
                </b-checkbox>
                <b-checkbox v-model="column.author"
                type="is-info">
                    Author
                </b-checkbox>
                <b-checkbox v-model="column.title"
                type="is-info">
                    Title
                </b-checkbox>
                <b-checkbox v-model="column.slug"
                type="is-info">
                    Slug
                </b-checkbox>
                <b-checkbox v-model="column.categories"
                type="is-info">
                    Categories
                </b-checkbox>
                <b-checkbox v-model="column.language"
                type="is-info">
                    Language
                </b-checkbox>
                <b-checkbox v-model="column.created_at"
                type="is-info">
                    Date Created
                </b-checkbox>
                <b-checkbox v-model="column.published_at"
                type="is-info">
                    Date Published
                </b-checkbox>
                <b-checkbox v-model="column.status"
                type="is-info">
                    Status
                </b-checkbox>
            
                <button class="is-medium bulk-edit" v-bind:class="{'button is-link':!editMode, 'button editMode':editMode}"
                    @click="bulkEditingFunc">
                    <span class="icon"><i class="fa fa-edit"></i></span>
                </button>
                @permission('delete-posts')
                <button class="button is-danger is-medium deleteItemBulk" v-if="editMode" v-on:click="deleteItemBulk"><span class="icon"><i class="fa fa-trash" style="margin-top: -10px;margin-right: -20px;"></i><i class="fa fa-trash"></i></span></button>
                @endpermission
                {{-- <button class="button is-info is-medium" v-if="editMode">Status</button> --}}
            </div>
            <div class="card-content">
                <table class="table is-narrow" v-if="posts.length">
                    <thead>
                        <tr>
                            {{-- <th>id</th> --}}
                            <th v-if="editMode"></th>
                            <th v-if="column.id" @click="changeItemOrder('id')">ID
                                <span class="icon is-small is-pulled-right" v-if="itemOrder.type == 'id'">
                                    <i v-if="itemOrder.direction == 'asc'" class="fa fa-arrow-circle-up"></i>
                                    <i v-if="itemOrder.direction == 'desc'" class="fa fa-arrow-circle-down"></i>
                                </span>
                            </th>
                            <th v-if="column.author" @click="changeItemOrder('author_id', 'name', 'users')">Author
                                <span class="icon is-small is-pulled-right" v-if="itemOrder.type == 'users'">
                                    <i v-if="itemOrder.direction == 'asc'" class="fa fa-arrow-circle-up"></i>
                                    <i v-if="itemOrder.direction == 'desc'" class="fa fa-arrow-circle-down"></i>
                                </span>
                            </th>
                            <th v-if="column.title" @click="changeItemOrder('title')">Title
                                <span class="icon is-small is-pulled-right" v-if="itemOrder.type == 'title'">
                                    <i v-if="itemOrder.direction == 'asc'" class="fa fa-arrow-circle-up"></i>
                                    <i v-if="itemOrder.direction == 'desc'" class="fa fa-arrow-circle-down"></i>
                                </span>
                            </th>
                            <th v-if="column.slug" @click="changeItemOrder('slug')">Slug
                                <span class="icon is-small is-pulled-right" v-if="itemOrder.type == 'slug'">
                                    <i v-if="itemOrder.direction == 'asc'" class="fa fa-arrow-circle-up"></i>
                                    <i v-if="itemOrder.direction == 'desc'" class="fa fa-arrow-circle-down"></i>
                                </span>
                            </th>
                            <th v-if="column.categories">Categories</th>
                            <th v-if="column.language" @click="changeItemOrder('language_id', 'name', 'languages')">Language
                                <span class="icon is-small is-pulled-right" v-if="itemOrder.type == 'language_id'">
                                    <i v-if="itemOrder.direction == 'asc'" class="fa fa-arrow-circle-up"></i>
                                    <i v-if="itemOrder.direction == 'desc'" class="fa fa-arrow-circle-down"></i>
                                </span>
                            </th>
                            {{-- <th>Excerpt</th> --}}
                            <th v-if="column.created_at" @click="changeItemOrder('created_at')">Date Created
                                <span class="icon is-small is-pulled-right" v-if="itemOrder.type == 'created_at'">
                                    <i v-if="itemOrder.direction == 'asc'" class="fa fa-arrow-circle-up"></i>
                                    <i v-if="itemOrder.direction == 'desc'" class="fa fa-arrow-circle-down"></i>
                                </span>
                            </th>
                            <th v-if="column.published_at" @click="changeItemOrder('published_at')">Date Published
                                <span class="icon is-small is-pulled-right" v-if="itemOrder.type == 'published_at'">
                                    <i v-if="itemOrder.direction == 'asc'" class="fa fa-arrow-circle-up"></i>
                                    <i v-if="itemOrder.direction == 'desc'" class="fa fa-arrow-circle-down"></i>
                                </span>
                            </th>
                            <th v-if="column.status" @click="changeItemOrder('status')">Status
                                <span class="icon is-small is-pulled-right" v-if="itemOrder.type == 'status'">
                                    <i v-if="itemOrder.direction == 'asc'" class="fa fa-arrow-circle-up"></i>
                                    <i v-if="itemOrder.direction == 'desc'" class="fa fa-arrow-circle-down"></i>
                                </span>
                            </th>
                            <th v-if="editMode && column.tools_bar "></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in posts">
                            <td v-if="editMode" class="row-small">
                                <div class="block">
                                    <b-checkbox v-model="bulkEditing" :native-value="item.id" type="is-info">
                                    </b-checkbox>
                                </div>
                            </td>
                            <td v-if="column.id" v-html="item.id"></td>
                            <td v-if="column.author" v-html="item.user.name"></td>
                            <td v-if="column.title" v-html="item.title"></td>
                            <td v-if="column.slug" v-html="item.slug"></td>
                            <td v-if="item.categories && column.categories">
                                <div>
                                    <span style="display: block; min-width:100px; border-bottom: solid thin gray;"
                                        v-for="item2 in item.categories" v-html="item2.name+'<br>'"></span>
                                </div>
                            </td>
                            <td v-if="!(item.categories) && column.categories"></td>
                            <td v-if="item.language && column.language" v-html="item.language.name"></td>
                            <td v-if="!(item.language) && column.language"></td>
                            <td v-if="column.created_at" v-html="item.created_at"></td>
                            <td v-if="column.published_at" v-html="item.published_at"></td>
                            <td class="has-text-centered" v-if="item.status && column.status">
                                <label v-bind:class="{'tag is-success':item.status}">@{{ item.status ? 'Publish' :
                                    'Draft' }}</label>
                            </td>
                            <td class="has-text-centered" v-if="!(item.status) && column.status">
                                <label class="tag is-info">@{{ item.status ? 'Publish' : 'Draft' }}</label>
                            </td>
                            <td v-if="editMode && column.tools_bar" class="has-text-centered  tools-bar" style="width: 150px">
                                @permission('read-posts')
                                <button class="button is-success" v-on:click="redirectItem(item)"><i class="fa fa-eye"></i></button>
                                @endpermission
                                @permission('update-posts')
                                <button class="button is-info" v-on:click="redirectItem(item, 'edit')"><i class="fa fa-pencil"></i></button>
                                @endpermission
                                @permission('delete-posts')
                                <button class="button is-danger" v-on:click="deleteItem(item)"><i class="fa fa-trash"></i></button>
                                @endpermission
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p v-if="!posts.length">
                There are no posts.
            </p>
        </div>
        <div class="pagination-wrapper m-t-20" style="display: none;" v-show="!isSiteLoading">
            <nav class="pagination is-centered">
                {{-- <a href="http://localhost:8000/manage/tags?page=1" rel="prev" class="pagination-previous">«</a>  --}}
                <ul class="pagination-list">
                    <li v-for="item in pageCount">
                        <div class="pagination-link" :class="[ item == currentPage ? 'is-current' : '' ]" @click="pageChange (item)">@{{item}}</div>
                    </li>
                </ul> 
                {{-- <a disabled="disabled" class="pagination-next">»</a> --}}
            </nav>
        </div>
        <b-loading :is-full-page="isFullPage" :active.sync="isLoading" :can-cancel="false"></b-loading>
        <b-modal :active.sync="errorLog" :can-cancel="['escape', 'outside']">
            <b-message :active.sync="errorLog" :title="errorLogTitle" type="is-danger" size="is-large" icon-pack="fa" icon-size="is-large" has-icon auto-close>
            <p class="has-text-danger" style="margin-top: 6px;" v-html="errorLogMsg"></p>
            </b-message>
        </b-modal>
    </div> <!-- end of .card -->
    {{-- @if ($posts != null)
    {{$posts->links()}}
    @endif --}}
</div>
@endsection

{{-- @endsection --}}
@section('scripts')
<script>
    var app = new Vue({
        el: '#app',
        data: {
            isFullPage: false,
            isLoading: false,
            formUrl: "{{route('posts.index')}}",
            isSiteLoading: false,
            name: '',
            token: "{{ csrf_token() }}",
            slug: '',
            api_token: '{{Auth::user()->api_token}}',
            waitingResponse: "S",
            errorLog: false,
            errorLogMsg: 'Testing!',
            errorLogTitle: '',
            seen: false,
            action: '{{ $posts_url }}',
            posts: {!!$postsJSON!!},
            bulkEditing: [],
            pageCount: {{ $posts->lastPage() }},
            currentPage: 1,
            editMode: false,
            itemOrder: {
                type: 'id',
                table: '',
                direction: 'desc',
                foreignType: ''
            },
            column:{
                id: false,
                author: true,
                title: true,
                slug: true,
                categories: true,
                language: true,
                created_at: true,
                published_at: false,
                status: true,
                tools_bar: true,
            },
            
        },
        methods: {
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
                }
            },
            pageChange: function (item) {
                this.currentPage = item;
                var vm = this;
                vm.isLoading = true;
                axios.post(vm.formUrl+'?page='+vm.currentPage, { 
                    orderBy: vm.itemOrder.type,
                    orderDir: vm.itemOrder.direction,
                    foreignType: vm.itemOrder.foreignType,
                    table: vm.itemOrder.table,
                    _token: vm.token,
                    _method: "GET"
                }) .then(function (response) { 
                    vm.isLoading = false;
                    vm.posts = response.data.postsJSON;
                    scroll(0,0)
                    console.log(response.data.postsJSON);
                    // vm.addingItemReInit();
                })
                .catch(function (error) { 
                    if(error.response.status === 404){
                            // vm.errorLog = true;
                            var errorLogMsg = 'Item does not seem to exist in the database.';
                            var errorLogTitle = 'Delete Error';
                            vm.alertCustomError(errorLogTitle, errorLogMsg);
                            vm.isLoading = false;
                            vm.postDataChanged('onresponse');
                            if( error.response.data )
                                vm.posts = error.response.data;
                            vm.bulkEditing = [];
                        }
                    if(error.response.status === 500){
                        // vm.errorLog = true;
                        var errorLogMsg = 'Something went wrong.';
                        var errorLogTitle = 'Error';
                        vm.alertCustomError(errorLogTitle, errorLogMsg);                        
                        vm.isLoading = false;
                        vm.postDataChanged('onresponse');
                        if( error.response.data )
                                vm.posts = error.response.data;
                        vm.bulkEditing = [];
                    }
                    console.log(error); 
                });
            },
            changeItemOrder: function (type, foreignType, table) {
                var vm = this;
                if(vm.itemOrder.type === type){
                    if(vm.itemOrder.direction == 'asc'){
                        vm.itemOrder.direction = 'desc';
                    }
                    else {
                        vm.itemOrder.direction = 'asc';
                    }
                }
                else vm.itemOrder.direction = 'desc';
                if(typeof table !== 'undefined'){
                    vm.itemOrder.table = table;
                }
                else{
                    vm.itemOrder.table = '';
                }
                if(typeof foreignType !== 'undefined'){
                    vm.itemOrder.foreignType = foreignType;
                }
                else{
                    vm.itemOrder.foreignType = '';
                }
                vm.itemOrder.type = type;
                vm.isLoading = true;
                axios.post(vm.formUrl+'?page='+vm.currentPage, { 
                    orderBy: vm.itemOrder.type,
                    orderDir: vm.itemOrder.direction,
                    foreignType: vm.itemOrder.foreignType,
                    table: vm.itemOrder.table,
                    _token: vm.token,
                    _method: "GET"
                }) .then(function (response) { 
                    vm.isLoading = false;
                    vm.posts = response.data.postsJSON;
                    scroll(0,0)
                    console.log(response.data.postsJSON);
                })
                .catch(function (error) { 
                    console.log(error); 
                });
            },
            deleteItem: function (item) {
                var vm = this;
                vm.postDataChanged('loading');
                vm.waitingResponse = "Waiting for response.";
                axios.post(vm.formUrl+ '/' + item.id, {
                        id: item.id,
                        _token: vm.token,
                        _method: "DELETE",
                    }).then(function (response) {
                        vm.postDataChanged('onresponse');
                        vm.posts = response.data;
                        console.log(response);
                        // vm.id = response.data.maxCol+1;
                        // vm.title = "";
                        // vm.slug = "";
                        // vm.idColMax = response.data.maxCol+1;
                        // vm.waitingResponse = "S";

                    })
                    .catch(function (error) {
                        window.error2 = error;
                        if(error.response.status === 404){
                            // vm.errorLog = true;
                            var errorLogMsg = 'Item does not seem to exist in the database.';
                            var errorLogTitle = 'Delete Error';
                            vm.alertCustomError(errorLogTitle, errorLogMsg);
                            vm.isLoading = false;
                            vm.postDataChanged('onresponse');
                            if( error.response.data )
                                vm.posts = error.response.data;
                            vm.bulkEditing = [];
                        }
                        if(error.response.status === 500){
                            // vm.errorLog = true;
                            var errorLogMsg = 'Something went wrong.';
                            var errorLogTitle = 'Error';
                            vm.alertCustomError(errorLogTitle, errorLogMsg);                            
                            vm.isLoading = false;
                            vm.postDataChanged('onresponse');
                            if( error.response.data )
                                vm.posts = error.response.data;
                            vm.bulkEditing = [];
                        }
                        console.log(error);
                    });
            },
            deleteItemBulk: function () {
                var vm = this;
                
                if(this.bulkEditing.length){
                    vm.postDataChanged('loading');
                    vm.waitingResponse = "Waiting for response.";
                    axios.post(vm.formUrl+ '/delete', {
                            ids: this.bulkEditing,
                            _token: vm.token,
                            _method: "DELETE",
                        }).then(function (response) {
                            vm.postDataChanged('onresponse');
                            vm.posts = response.data;
                        })
                        .catch(function (error) {
                            window.error2 = error;
                            if(error.response.status === 404){
                                // vm.errorLog = true;
                                var errorLogMsg = 'Items you selected do not seem to exist in the database.';
                                var errorLogTitle = 'Delete Error';
                                vm.alertCustomError(errorLogTitle, errorLogMsg);      
                                vm.isLoading = false;
                                vm.postDataChanged('onresponse');
                                if( error.response.data )
                                    vm.posts = error.response.data;
                                vm.bulkEditing = [];
                            }
                            if(error.response.status === 500){
                                // vm.errorLog = true;
                                var errorLogMsg = 'Something went wrong.';
                                var errorLogTitle= 'Error';
                                vm.alertCustomError(errorLogTitle, errorLogMsg);
                                vm.isLoading = false;
                                vm.postDataChanged('onresponse');
                                if( error.response.data )
                                    vm.posts = error.response.data;
                                vm.bulkEditing = [];
                            }
                            console.log(error);
                        });
                }
                else{
                    // vm.errorLog = true;
                    var errorLogMsg = 'No items selected.';
                    var errorLogTitle = 'Empty Delete Queue';
                    vm.alertCustomError(errorLogTitle, errorLogMsg);
                    vm.isLoading = false;
                }
            },
            redirectItem: function (params, add) {
                if (typeof add === 'undefined') add = '';
                window.location.href = "{!!url()->current()!!}" + "/" + params.id + '/' + add;
            },
            bulkEditingFunc: function (params) {
                this.editMode = !this.editMode;
                this.bulkEditing = [];
            },
            alertCustomError: function (title, msg) {
                this.$dialog.alert({
                    title: title,
                    message: msg,
                    type: 'is-danger',
                    hasIcon: true,
                    icon: 'times-circle',
                    iconPack: 'fa',
                    canCancel: ['escape, outside']
                });
            },
            
        },
        mounted:function(){
            window.addEventListener("load", function(event) {
                this.isSiteLoading = false;
            });
        },
        watch:{
            bulkEditing: function(){
                if(this.bulkEditing.length){
                    this.column.tools_bar = false;
                }
                else{
                    this.column.tools_bar = true;
                }
            }
        }  
    });
</script>
@endsection