@extends('layouts.manage', ['title_text' => $title_text, 'item_type' => $item_type])
@section('content')
    <div class="card m-b-10">
        <div class="card-content tools-bar" style="display: none;" v-show="!isSiteLoading">
            <table class="table is-narrow add-cat">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        {{-- @if($languages) --}}
                            @if($item_type !== 'languages')
                                <th>Description</th>
                                <th>Language</th>
                            @endif
                        {{-- @endif --}}
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td v-html="idColMax">{{ $idColMax+1 }}</td>
                        <td>
                            <div class="new-item"> 
                                <div class="">
                                    <input type="text" class="input" name="name" id="" v-model="title" @input="onTitleChange (title, slug, 'new')">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="columns new-item"> 
                                <div class="column">
                                    <input class="input" :class="{'is-danger': slugExists}" type="text" name="slug" v-model="slug" v-if="!newUpdateSlugEdit" @input="setSlug(slug, false, idColMax)">
                                    <input class="input" :class="{'is-danger': slugExists}" v-model="slug" v-if="newUpdateSlugEdit" disabled/>
                                </div>
                                <div class="column is-narrow">
                                    <b-switch :value="false"
                                        v-model="newUpdateSlugEdit"
                                        type="is-success"
                                        @input="updateSlugOnClick ('new')">
                                    </b-switch>
                                </div>
                            </div>
                        </td>
                        {{-- @if($languages) --}}
                            @if($item_type !== 'languages')
                                <td><input type="text" class="input" name="desc" id="" v-model="desc" style="width: 280px;"></td>
                                <td v-if="language_id">
                                    <div class="select is-info" style="min-width:150px">
                                        <select style="width:100%" name="language_id" v-model="language_id">
                                            @foreach ($languages as $language)
                                                <option value="{{ $language->id }}">{{$language->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                @else
                                <?php
                                    $languages = json_encode([0 => '']);
                                ?>
                            @endif
                        {{-- @endif --}}
                    </tr>
                </tbody>
            </table>
            <button class="button is-success" v-on:click="sendStore" style="
                display:  block;
                margin-left:  auto;
                margin-right:  0;">
                <i class="fa fa-send m-r-10"></i>Submit</button>
        </div>
        <b-loading :is-full-page="isFullPage" :active.sync="isLoading" :can-cancel="false"></b-loading>
    </div>
    <div class="card p-b-25 m-b-25">
        <div class="bulk-edit-tools tools-bar" style="display: none;" v-show="!isSiteLoading" v-if="items.length">
            @permission('update-general')
            <button class="is-medium bulk-edit" v-bind:class="{'button is-link':!editMode, 'button editMode':editMode}" @click="bulkEditingFunc">
                <span class="icon"><i class="fa fa-edit"></i></span>
            </button>
            @endpermission
            @permission('delete-general')
            <button class="button is-danger is-medium deleteItemBulk" v-if="editMode" v-on:click="deleteItemBulk"><span class="icon"><i class="fa fa-trash" style="margin-top: -10px;margin-right: -20px;"></i><i class="fa fa-trash"></i></span></button>
            @endpermission
        </div>
        
        <div class="card-content" style="display: none;" v-show="!isSiteLoading">
            <table class="table is-narrow index-cat custom-table" v-if="items.length">
                <thead>
                    <tr>
                        <th v-if="editMode"></th>
                        <th @click="changeItemOrder('id')" class="row-small">
                            <div class="is-unselectable" style="overflow: auto;">
                                <span class="is-pulled-left">ID </span>
                                <span class="icon is-small is-pulled-right" v-if="itemOrder.type == 'id'">
                                    <i v-if="itemOrder.direction == 'asc'" class="fa fa-arrow-circle-up"></i>
                                    <i v-if="itemOrder.direction == 'desc'" class="fa fa-arrow-circle-down"></i>
                                </span>
                            </div>
                        </th>
                        <th @click="changeItemOrder('name')">
                            <div class="is-unselectable" style="overflow: auto;">
                                <span class="is-pulled-left">Name </span>
                                <span class="icon is-small is-pulled-right" v-if="itemOrder.type == 'name'">
                                    <i v-if="itemOrder.direction == 'asc'" class="fa fa-arrow-circle-up"></i>
                                    <i v-if="itemOrder.direction == 'desc'" class="fa fa-arrow-circle-down"></i>
                                </span>
                            </div>
                        </th>
                        <th @click="changeItemOrder('slug')">
                            <div class="is-unselectable" style="overflow: auto;">
                                <span class="is-pulled-left">Slug </span>
                                <span class="icon is-small is-pulled-right" v-if="itemOrder.type == 'slug'">
                                    <i v-if="itemOrder.direction == 'asc'" class="fa fa-arrow-circle-up"></i>
                                    <i v-if="itemOrder.direction == 'desc'" class="fa fa-arrow-circle-down"></i>
                                </span>
                            </div>
                        </th>
                        @if($item_type !== 'languages')
                            <th @click="changeItemOrder('description')">
                                <div class="is-unselectable" style="overflow: auto;">
                                    <span class="is-pulled-left">Description </span>
                                    <span class="icon is-small is-pulled-right" v-if="itemOrder.type == 'description'">
                                        <i v-if="itemOrder.direction == 'asc'" class="fa fa-arrow-circle-up"></i>
                                        <i v-if="itemOrder.direction == 'desc'" class="fa fa-arrow-circle-down"></i>
                                    </span>
                                </div>
                            </th>
                            {{-- @if($languages) --}}
                                <th @click="changeItemOrder('language_id', 'name', 'languages')">
                                    <div class="is-unselectable" style="overflow: auto;">
                                        <span class="is-pulled-left">Language </span>
                                        <span class="icon is-small is-pulled-right" v-if="itemOrder.type == 'language_id'">
                                            <i v-if="itemOrder.direction == 'asc'" class="fa fa-arrow-circle-up"></i>
                                            <i v-if="itemOrder.direction == 'desc'" class="fa fa-arrow-circle-down"></i>
                                        </span>
                                    </div>
                                </th>
                            {{-- @endif --}}
                        @endif
                        <th style="width: 10px;" v-if="editMode && column.tools_bar"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!seen" v-for="(item, index) in items" :key="item.id">
                        <td v-if="editMode" class="row-small">
                            <div class="block">
                                <b-checkbox 
                                    v-model="bulkEditing"
                                    :native-value="item.id"
                                    type="is-info">
                                </b-checkbox>
                                
                            </div>
                        </td>
                        <td v-html="item.id" class="row-small"></td>
                        <td>
                            <p v-html="titleEdit[index]" v-if="!(fieldEditMode==index)"></p>
                            <input class="input" type="text" v-if="fieldEditMode==index" v-model="titleEdit[index]" name="name" @input="onTitleChange (titleEdit[index], slugEdit[index], index)">
                        </td>
                        <td>
                            <p v-html="slugEdit[index]" v-if="!(fieldEditMode==index)"></p>
                            <div class="input-custom" v-if="fieldEditMode==index"> 
                                <div>
                                    <input class="input" :class="{'is-danger': slugExistsUp}" type="text" name="slug" v-model="slugEdit[index]" @input="setSlug(slugEdit[index], true, item.id)">
                                </div>
                                <div class="field">
                                    <div>
                                        <b-switch :value="false"
                                            v-model="updateSlugEdit"
                                            v-if="fieldEditMode==index"
                                            type="is-success"
                                            @input="updateSlugOnClick (index, item.id)">
                                        </b-switch>
                                        <button class="button is-warning is-small" @click="resetSlugInput (item.name, item.slug, index)">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </td>
                        @if($item_type !== 'languages')
                            <td>
                                <p v-html="descEdit[index]" v-if="!(fieldEditMode==index)"></p>
                                <input class="input" type="text" v-if="fieldEditMode==index" v-model="descEdit[index]" name="desc">
                            
                            </td>
                            {{-- @if($languages) --}}
                                <td v-if="item.language" class="row-small">
                                    <p v-html="item.language.name" v-if="!(fieldEditMode==index)&&item.language"></p>
                                    <div class="select is-info" style="width:100%" v-if="fieldEditMode==index&&item.language">
                                        <select style="width:100%" v-model="language_idEdit[index]">
                                            <option v-if="item.language" :value="item.language.id" v-html="item.language.name"></option>
                                            <option v-for="(item2, index2) in languages" v-if="item2.slug != item.language.slug" :value="item2.id" v-html="item2.name"></option>
                                        </select>
                                    </div>
                                </td>
                                <td v-else></td>                        
                            {{-- @endif --}}
                        @endif
                        <td class="has-text-centered tools-bar" style="width: 150px" v-if="editMode && column.tools_bar">
                            {{-- <button class="button is-success" v-on:click="redirectItem(item)" v-if="fieldEditMode!=index"><i class="fa fa-eye"></i></button> --}}
                            @permission('update-general')
                            <button class="button is-info" @click="processUpdateSlugEdit ('initItemEdit', item.name, item.description, item.slug, index)" v-if="fieldEditMode!=index"><i class="fa fa-pencil"></i></button>
                            <button class="button is-info" @click="updateStore (item.id, titleEdit[index], descEdit[index], slugEdit[index], language_idEdit[index])" v-if="fieldEditMode==index"><i class="fa fa-send"></i></button>
                            @endpermission
                            @permission('delete-general')
                            <button class="button is-danger" v-on:click="deleteItem(item)" v-if="fieldEditMode!=index"><i class="fa fa-trash"></i></button>
                            <button class="button is-danger" @click="processUpdateSlugEdit ('cancelItemEdit', item.name, item.description, item.slug, index)" v-if="fieldEditMode==index"><i class="fa fa-ban"></i></button>
                            @endpermission
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else>There are no items here.</div>   
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
    </div>
@endsection
@section('scripts')
<script>
    const CancelToken = axios.CancelToken;
    let cancel;
    var app = new Vue({
        el: '#app',
        data: {
            isFullPage: false,
            isLoading: false,
            isSiteLoading: false,
            id: {{ $idColMax+1 }},
            token: "{{ csrf_token() }}",
            name: '',
            slug: '',
            slugExists: false,
            slugExistsUp: false,
            slugEdit: [],
            api_token: '{{Auth::user()->api_token}}',
            errorLog: false,
            errorLogMsg: '',
            errorLogTitle: '',
            groupId: "Test",
            items: {!! json_encode($itemsJSON["items"]) !!},
            idColMax: {{ $idColMax+1 }},
            languages: {!! $languages?$languages:1 !!},
            waitingResponse: "S",
            title: '',
            titleEdit: [],
            desc: '',
            descEdit: [],
            language: '',
            language_id: 1,
            language_idEdit: [],
            seen: false,
            action: '{{ $items_url }}',
            bulkEditing: [],
            editMode: false,
            fieldEditMode: null,
            updateSlugEdit: false,
            newUpdateSlugEdit: {{$item_type !== 'languages'?'true':'false'}},
            currentPage: 1,
            pageCount: {{ $items->lastPage() }},
            ctrlURL: '/manage/{{ $item_type }}/',
            item_type: '{{ $item_type }}',
            itemOrder: {
                type: 'id',
                direction: 'desc',
                foreignType: '',
                table: ''
            },
            column:{
                
                tools_bar: true,
            }
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
            setSlug: _.debounce(function (newVal, upload, id) {
                if (newVal === "") return "";
                var slug = Slug(newVal);
                var vm = this;
                if (this.api_token && slug) {
                    axios.post(siteUrl+"/api/unique/slug/"+vm.item_type, {
                            api_token: vm.api_token,
                            slug: slug,
                            id: id
                        }, 
                        {
                            cancelToken: new CancelToken(function executor(c) {
                                // An executor function receives a cancel function as a parameter
                                cancel = c;
                            })
                        }
                        )
                        .then(function(response) {
                            if (response.data) {
                                if(!upload) vm.slugExists = true;
                                else vm.slugExistsUp = true;
                            } else {
                                if(!upload) vm.slugExists = false;
                                else vm.slugExistsUp = false;
                            }
                        })
                        .catch(function(error) {
                        console.log(error);
                    });
                }
            }, 400),
            pageChange: function (item) {
                this.currentPage = item;
                var vm = this;
                vm.isLoading = true;
                axios.post(siteUrl+vm.ctrlURL+'?page='+vm.currentPage, { 
                        orderBy: vm.itemOrder.type,
                        orderDir: vm.itemOrder.direction,
                        foreignType: vm.itemOrder.foreignType,
                        table: vm.itemOrder.table,
                        _token: vm.token,
                        _method: "GET"
                    }) .then(function (response) { 
                        vm.isLoading = false;
                        vm.items = response.data.items;
                        vm.addingItemReInit();
                    })
                    .catch(function (error) { 
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
                if(typeof foreignType !== 'undefined'){
                    vm.itemOrder.foreignType = foreignType;
                }
                else{
                    vm.itemOrder.foreignType = '';
                }
                if(typeof table !== 'undefined'){
                    vm.itemOrder.table = table;
                }
                else{
                    vm.itemOrder.table = '';
                }
                vm.itemOrder.type = type;
                vm.isLoading = true;
                axios.post(siteUrl+vm.ctrlURL+'?page='+vm.currentPage, { 
                    orderBy: vm.itemOrder.type,
                    orderDir: vm.itemOrder.direction,
                    foreignType: vm.itemOrder.foreignType,
                    table: vm.itemOrder.table,
                    _token: vm.token,
                    _method: "GET"
                }) .then(function (response) { 
                    vm.isLoading = false;
                    vm.items = response.data.items;
                    vm.addingItemReInit();
                })
                .catch(function (error) { 
                    console.log(error); 
                });
            },
            onTitleChange: function(valName, valSlug, index) {
                if(this.updateSlugEdit){
                    this.slugEdit[index] = Slug(valName);
                    this.setSlug(this.slugEdit[index], true);
                }
                if(this.newUpdateSlugEdit && index === 'new') {
                    this.slug = Slug(valName);
                    this.setSlug(this.slug);
                }
            },
            resetSlugInput: function(valName, valSlug, index) {
                this.updateSlugEdit = false;
                this.slugExistsUp = false;
                Vue.set(this.slugEdit, index, valSlug);
                console.log(Math.random());
            }, 
            updateSlugOnClick: function(index, id) {
                if(this.updateSlugEdit){
                    Vue.set(this.slugEdit, index, Slug(this.titleEdit[index]));
                    this.setSlug(this.slugEdit[index], true, id);
                }
                if(this.newUpdateSlugEdit && index === 'new') this.slug = Slug(this.title);
            },
            processUpdateSlugEdit: function(val, valName, valDesc, valSlug, index) {
                if (typeof index == 'undefined') index = null;
                if(val == 'cancelItemEdit'){
                    this.updateSlugEdit = false;
                    this.fieldEditMode = null;
                    Vue.set(this.slugEdit, index, valSlug);
                    Vue.set(this.titleEdit, index, valName);
                    Vue.set(this.descEdit, index, valDesc);
                }
                if(val == 'initItemEdit'){
                    this.updateSlugEdit = false;
                    this.fieldEditMode = index;
                }
            }, 
            updateSlug: function(val) { 
                this.slug = val; 
            }, 
            slugCopied: function(type, msg, val) { 
                notifications.toast(msg, {type: `is-${type}`
                });
            },
            sendStore: function(){
                var vm = this;
                vm.item_type == 'languages' ? vm.language_id = true : vm.language_id;
                vm.item_type == 'languages' ? vm.desc = false : vm.desc;
                vm.waitingResponse = "Waiting for response.";
                var json = { 
                    _token: vm.token,
                    id: vm.id,
                    name: vm.title, 
                    desc: vm.desc, 
                    slug: vm.slug,
                    orderBy: vm.itemOrder.type,
                    orderDir: vm.itemOrder.direction,
                };
                json.item_type = vm.item_type;
                if(vm.item_type !== 'languages'){
                    json.language_id = vm.language_id;
                }
                if(vm.title && vm.slug && vm.language_id ){
                    vm.isLoading = true;
                    axios.post(siteUrl+vm.ctrlURL+'?page='+vm.currentPage, json) .then(function (response) { 
                        cancel();
                        vm.isLoading = false;
                        console.log('The request cancelled');
                        if(typeof vm.items !== 'undefined')
                            vm.items = response.data.items;
                        console.log(response);
                        vm.id = vm.id+1;
                        vm.title = "";
                        vm.slug = "";
                        vm.desc = "";
                        vm.idColMax += 1;
                        vm.pageCount = response.data.pageCount;
                        vm.slugExists = false;
                        vm.waitingResponse = "S";
                        vm.addingItemReInit();
                    })
                    .catch(function (error) { 
                        
                        console.log(error); 
                    });
                }else{
                    var errorLogTitle = 'Send Error';
                    var errorLogMsg = 'Missing some input fields: <p><ul style="margin-left:35px;list-style-type: circle;">';
                    if(!vm.title){
                        // errorLog = true;
                        errorLogMsg += '<li> Title </li>';
                        
                    }
                    if(!vm.slug){
                        // errorLog = true;
                        errorLogMsg += '<li> Slug </li>';
                    }
                    if(!vm.language_id && vm.item_type !== 'languages'){
                        // errorLog = true;
                        errorLogMsg += '<li> Language </li>';
                    }
                    errorLogMsg += '</ul></p>';
                    vm.alertCustomError(errorLogTitle, errorLogMsg);
                }

            },
            updateStore: function(id, title, desc, slug, language_id ){
                var vm = this;
                vm.item_type == 'languages' ? language_id = true : language_id;
                vm.waitingResponse = "Waiting for response.";
                var json = { 
                    id: id,
                        name: title, 
                        desc: desc, 
                        slug: slug,
                        orderBy: vm.itemOrder.type,
                        orderDir: vm.itemOrder.direction,
                        _token: vm.token,
                        _method: "PUT"
                };
                json.item_type = vm.item_type;
                if(vm.item_type !== 'languages'){
                    json.language_id = language_id;
                }
                if(title && slug && language_id){
                    vm.isLoading = true;
                    axios.post(siteUrl+vm.ctrlURL+id+'?page='+vm.currentPage, json) .then(function (response) { 
                        vm.isLoading = false;
                        if(typeof vm.items !== 'undefined')
                            vm.items = response.data.items;
                        vm.updateSlugEdit = false;
                        vm.fieldEditMode = null;
                        console.log(response);
                        vm.addingItemReInit();
                        vm.waitingResponse = "S";
                    })
                    .catch(function (error) { 
                        window.error = error;
                        if(error.response.status === 404){
                            // vm.errorLog = true;
                            var errorLogMsg = 'Item does not seem to exist in the database.';
                            var errorLogTitle = 'Delete Error';
                            vm.alertCustomError(errorLogTitle, errorLogMsg);
                            vm.isLoading = false;
                            if(typeof vm.items !== 'undefined')
                                vm.items =  error.response.data.items;
                            vm.id =  error.response.data.maxCol+1;
                            vm.idColMax =  error.response.data.maxCol+1;
                            vm.pageCount =  error.response.data.pageCount;
                            if(vm.currentPage > vm.pageCount) vm.currentPage = vm.pageCount;
                            vm.addingItemReInit();
                            vm.fieldEditMode = null;
                        }
                        if(error.response.status === 500){
                            // vm.errorLog = true;
                            var errorLogMsg = 'Something went wrong.';
                            var errorLogTitle = 'Error';
                            vm.alertCustomError(errorLogTitle, errorLogMsg);
                            vm.isLoading = false;
                            vm.postDataChanged('onresponse');
                            if(typeof vm.items !== 'undefined')
                                vm.posts = error.response.data;
                            vm.bulkEditing = [];
                            vm.fieldEditMode = null;
                        }
                        console.log(error); 
                    });
                }else{
                    var errorLogTitle = 'Send Error';
                    var errorLogMsg = 'Missing some input fields: <p><ul style="margin-left:35px;list-style-type: circle;">';
                    if(!title){
                        // var errorLog = true;
                        errorLogMsg += '<li> Title </li>';
                        
                    }
                    if(!slug){
                        // var errorLog = true;
                        errorLogMsg += '<li> Slug</li>';
                    }
                    if(!vm.language_id && vm.item_type !== 'languages'){
                        // var errorLog = true;
                        errorLogMsg += '<li> Language</li>';
                    }
                    errorLogMsg += '</ul></p>';
                    vm.alertCustomError(errorLogTitle, errorLogMsg);
                }
            },
            deleteItem: function(item){
                var vm = this;
                var deleteItemInside = function () {
                    vm.waitingResponse = "Waiting for response.";
                    vm.isLoading = true;
                    axios.post(siteUrl+vm.ctrlURL+item.id, { 
                        id: item.id,
                        orderBy: vm.itemOrder.type,
                        orderDir: vm.itemOrder.direction,
                        _token: vm.token,
                        _method: "DELETE",
                    }) .then(function (response) { 
                        vm.isLoading = false;
                        if( response.data.error_message ){
                            // vm.errorLog = true;
                            var errorLogMsg = 'You are required to have at least one language!';
                            var errorLogTitle = 'Language Required';
                            vm.alertCustomError(errorLogTitle, errorLogMsg);                        
                        }
                        // else{
                        //     vm.errorLog = false;
                        // }
                        if(typeof vm.items !== 'undefined')
                            vm.items = response.data.items;
                        console.log(response);
                        vm.id = response.data.maxCol+1;
                        vm.idColMax = response.data.maxCol+1;
                        vm.pageCount = response.data.pageCount;
                        if(vm.currentPage>vm.pageCount)vm.currentPage = vm.pageCount;
                        vm.addingItemReInit();
                    })
                    .catch(function (error) { 
                        if(error.response.status === 404){
                            // vm.errorLog = true;
                            var errorLogMsg = 'Item does not seem to exist in the database.';
                            var errorLogTitle = 'Delete Error';
                            vm.alertCustomError(errorLogTitle, errorLogMsg);
                            vm.isLoading = false;
                            if(typeof vm.items !== 'undefined')
                                vm.items =  error.response.data.items;
                            vm.id =  error.response.data.maxCol+1;
                            vm.idColMax =  error.response.data.maxCol+1;
                            vm.pageCount =  error.response.data.pageCount;
                            if(vm.currentPage > vm.pageCount) vm.currentPage = vm.pageCount;
                            vm.addingItemReInit();
                        }
                        if(error.response.status === 500){
                            // vm.errorLog = true;
                            var errorLogMsg = 'Something went wrong.';
                            var errorLogTitle = 'Error';
                            vm.alertCustomError(errorLogTitle, errorLogMsg);
                            vm.isLoading = false;
                            vm.postDataChanged('onresponse');
                            if(typeof vm.items !== 'undefined')
                                vm.posts = error.response.data;
                            vm.bulkEditing = [];
                        }
                        console.log(error); 
                    });
                };
                if(vm.item_type == 'languages'){
                    vm.confirmLangDelete(
                        deleteItemInside
                    );
                }
                else{
                    deleteItemInside();
                }
            },
            deleteItemBulk: function(){
                var vm = this;
                if(vm.bulkEditing.length){
                    var deleteItemInsideBulk = function () {
                        vm.waitingResponse = "Waiting for response.";
                        vm.isLoading = true;
                            axios.post(siteUrl+vm.ctrlURL+'delete', { 
                                ids: vm.bulkEditing,
                                action: 'deleteBulk',
                                orderBy: vm.itemOrder.type,
                                orderDir: vm.itemOrder.direction,
                                _token: vm.token,
                                _method: "DELETE",
                            }) .then(function (response) { 
                                vm.bulkEditing = [];
                                vm.isLoading = false;
                                if(typeof vm.items !== 'undefined')
                                    vm.items = response.data.items;
                                vm.id = response.data.maxCol+1;
                                vm.idColMax = response.data.maxCol+1;
                                vm.pageCount = response.data.pageCount;
                                if(vm.currentPage > vm.pageCount) vm.currentPage = vm.pageCount;
                                vm.addingItemReInit();
                            })
                            .catch(function (error) { 
                                if(error.response.status === 404){
                                    //vm.errorLog = true;
                                    var errorLogMsg = 'Items you selected do not seem to exist in the database.';
                                    var errorLogTitle = 'Delete Error';
                                    vm.alertCustomError(errorLogTitle, errorLogMsg);
                                    vm.isLoading = false;
                                    vm.items =  error.response.data.items;
                                    vm.id =  error.response.data.maxCol+1;
                                    vm.idColMax =  error.response.data.maxCol+1;
                                    vm.pageCount =  error.response.data.pageCount;
                                    if(vm.currentPage > vm.pageCount) vm.currentPage = vm.pageCount;
                                    vm.addingItemReInit();
                                    vm.bulkEditing = [];
                                }
                                if(error.response.status === 500){
                                    // vm.errorLog = true;
                                    var errorLogMsg = 'Something went wrong.';
                                    var errorLogTitle = 'Error';
                                    vm.alertCustomError(errorLogTitle, errorLogMsg);
                                    vm.isLoading = false;
                                    vm.postDataChanged('onresponse');
                                    vm.posts = error.response.data;
                                    vm.bulkEditing = [];
                                }
                                console.log(error); 
                            });
                        
                    };
                    if(vm.item_type == 'languages'){
                        vm.confirmLangDelete(
                            deleteItemInsideBulk
                        );
                    }
                    else{
                        deleteItemInsideBulk();
                    }
                }
                else{
                    // vm.errorLog = true;
                    var errorLogMsg = 'No items selected.';
                    var errorLogTitle = 'Empty Delete Queue';
                    vm.alertCustomError(errorLogTitle, errorLogMsg);

                    vm.isLoading = false;
                }
            },
            missingInputAlert: function (title, slug, language_id) {

            },
            redirectItem: function (params) {
                window.location.href = "{!!url()->current()!!}"+"/"+params.id;
            },
            bulkEditingFunc: function (params) {
                this.editMode=!this.editMode;
                this.bulkEditing = [];
            },
            addingItemReInit: function () {
                var outS=[], outN=[], outD=[], outL=[];
                if(typeof this.items !== 'undefined'){
                    for (var i=0; i<this.items.length; i++ ) {
                        if(this.items[i]){
                            outS.push(this.items[i].slug);
                            outN.push(this.items[i].name);
                            outD.push(this.items[i].description);
                            outL.push(this.items[i].language_id);
                        }
                    }
                }
                this.slugEdit = outS;
                this.titleEdit = outN;
                this.descEdit = outD;
                this.language_idEdit = outL;
            },
            confirmLangDelete: function (input) {
                if(this.item_type == 'languages'){
                    this.$dialog.confirm({
                        title: 'Deleting language',
                        message: 
                            '<h4><p>Are you sure you want to <b>delete</b> this language?</p>' + 
                            '<p style="color:firebrick">This action will delete all connected posts, categories and tags.</p>' +
                            '<p style="color:red"><b style="font-weight:900">You cannot undo this.</b></p></h4>',
                        confirmText: 'Delete Language',
                        type: 'is-danger',
                        hasIcon: true,
                        iconPack: 'fa',
                        onConfirm: function (){
                            // this.$toast.open('Account deleted!');
                            return input();
                        }
                    });
                }
            },
            alertCustomError: function (title, msg) {
                this.$dialog.alert({
                    title: title,
                    message: msg,
                    type: 'is-danger',
                    hasIcon: true,
                    icon: 'times-circle',
                    iconPack: 'fa',
                    canCancel: ['escape', 'outside']
                });
            }
        },
        watch: {
            // slug: function () { 
            //     if(this.item_type == 'languages'){
            //         var string = this.slug;
            //         var length = 2;
            //         this.slug = string.substring(0, length);
            //     }
            // },
            bulkEditing: function () { 
                if(this.bulkEditing.length){
                    this.column.tools_bar = false;
                }
                else{
                    this.column.tools_bar = true;
                }
            }
        },
        created: function () { 
            var vm =this;
            vm.addingItemReInit();
        },
        mounted:function(){
            this.isSiteLoading = false;
        }
    });
</script>
@endsection