@extends('layouts.manage') 
@section('content')
<div class="flex-container">
    <div class="columns m-t-10">
        <div class="column">
            <h1 class="title">This is the {{ $item_type }}.index page</h1>
        </div>
        <div class="column">
        </div>
    </div>
    <hr class="m-t-0">
    <div class="card m-b-10">
        <div class="card-content tools-bar">
            <table class="table is-narrow add-cat">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        @if($item_type !== 'languages')
                            <th>Description</th>
                            <th>Language</th>
                        @endif
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
                                    <input class="input" :class="{'is-danger': slugExists}" type="text" name="slug" v-model="slug" v-if="!newUpdateSlugEdit">
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
                    </tr>
                </tbody>
            </table>
            <button class="button is-warning" v-on:click="sendStore" style="
                display:  block;
                margin-left:  auto;
                margin-right:  0;">
                <i class="fa fa-user-plus m-r-10"></i>
                Create New Tag</button>
        </div>
    </div>
    <div class="card">
        <div class="bulk-edit-tools tools-bar" style="">
            
            <div class="per-page select">
                <select name="per-page" id="per-page">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="25">25</option>
                </select>
            </div>
            @role('superadmin|admin|editor')
            <button class="is-medium" v-bind:class="{'button is-link':!editMode, 'button editMode':editMode}" @click="bulkEditingFunc">
                <span class="icon"><i class="fa fa-edit"></i></span>
            </button>
            <button class="button is-danger is-medium" v-if="editMode" v-on:click="deleteItemBulk"><span class="icon"><i class="fa fa-trash"></i></span></button>
            @endrole
        </div>
        
        <div class="card-content">
            <b-modal :active.sync="errorLangReq" :can-cancel="['escape', 'outside']">
                <b-message :active.sync="errorLangReq" title="Delete Error" type="is-danger" size="is-large" icon-pack="fa" icon-size="is-large" has-icon auto-close>
                    <p class="has-text-danger" style="margin-top: 6px;"> You are required to have at least one language!</p>
                </b-message>
            </b-modal>
            <hr>
            <b-table :data="json" :columns="columns"></b-table>

        </div>
    </div>
    <div class="pagination-wrapper m-t-20">
        <nav class="pagination is-centered">
            <a href="http://localhost:8000/manage/tags?page=1" rel="prev" class="pagination-previous">«</a> 
            <ul class="pagination-list">
                <li v-for="item in pageCount">
                    <div class="pagination-link" :class="[ item == currentPage ? 'is-current' : '' ]" @click="pageChange (item)">@{{item}}</div>
                </li>
            </ul> 
            <a disabled="disabled" class="pagination-next">»</a>
        </nav>
    </div>
</div>
@endsection
@section('scripts')
<script>
    var app = new Vue({
        el: '#app',
        data: {
            id: {{ $idColMax+1 }},
            json: {!! $json !!},
            columns: [
                    {
                        field: 'id',
                        label: 'ID',
                        width: '40',
                        numeric: true
                    },
                    {
                        field: 'name',
                        label: 'Name',
                    },
                    {
                        field: 'slug',
                        label: 'Slug',
                    }
                ],
            token: "{{ csrf_token() }}",
            name: '',
            slug: '',
            slugExists: false,
            slugExistsUp: false,
            slugEdit: [],
            api_token: '{{Auth::user()->api_token}}',
            errorLangReq: false,
            groupId: "Test",
            items: {!! $itemsJSON !!}.items,
            idColMax: {{ $idColMax+1 }},
            languages: {!! $languages !!},
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
            newUpdateSlugEdit: true,
            currentPage: 1,
            pageCount: {{ $items->lastPage() }},
            ctrlURL: '/manage/{{ $item_type }}/',
            item_type: '{{ $item_type }}',
            itemOrder: {
                type: 'id',
                direction: 'desc'
            }
        },
        methods: {
            setSlug: _.debounce(function (newVal, upload) {
                if (newVal === "") return "";
                var slug = Slug(newVal);
                var vm = this;
                if (this.api_token && slug) {
                    axios.post(siteUrl+"/api/unique/slug/"+vm.item_type, {
                            api_token: vm.api_token,
                            slug: slug
                        })
                        .then(function(response) {
                            if (response.data) {
                                if(!upload) vm.slugExists = true;
                                else vm.slugExistsUp = true;
                                // vm.slug;
                            } else {
                                if(!upload) vm.slugExists = false;
                                else vm.slugExistsUp = false;
                                // vm.setSlug(newVal);
                            }
                        })
                        .catch(function(error) {
                        console.log(error);
                    });
                }
            }, 3500),
            pageChange: function (item) {
                this.currentPage = item;
                var vm = this;
                axios.post(this.ctrlURL+'?page='+vm.currentPage, { 
                        orderBy: vm.itemOrder.type,
                        orderDir: vm.itemOrder.direction,
                        _token: vm.token,
                        _method: "GET"
                    }) .then(function (response) { 
                        vm.items = response.data.items;
                        vm.addingItemReInit();
                    })
                    .catch(function (error) { 
                        console.log(error); 
                    });
            },
            changeItemOrder: function (type) {
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
                vm.itemOrder.type = type;
                axios.post(siteUrl+vm.ctrlURL+'?page='+vm.currentPage, { 
                    orderBy: vm.itemOrder.type,
                    orderDir: vm.itemOrder.direction,
                    _token: vm.token,
                    _method: "GET"
                }) .then(function (response) { 
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
            updateSlugOnClick: function(index) {
                if(this.updateSlugEdit){
                    Vue.set(this.slugEdit, index, Slug(this.titleEdit[index]));
                    this.setSlug(this.slugEdit[index], true);
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
                vm.waitingResponse = "Waiting for response.";
                if(vm.title && vm.slug && vm.language_id ){
                    axios.post(this.ctrlURL+'?page='+vm.currentPage, { 
                        id: vm.id,
                        name: vm.title, 
                        desc: vm.desc, 
                        slug: vm.slug,
                        language_id: vm.language_id,
                        orderBy: vm.itemOrder.type,
                        orderDir: vm.itemOrder.direction,
                    }) .then(function (response) { 
                        vm.items = response.data.items;
                        console.log(response);
                        vm.id = vm.id+1;
                        vm.title = "";
                        vm.slug = "";
                        vm.desc = "";
                        vm.idColMax += 1;
                        vm.waitingResponse = "S";
                        vm.addingItemReInit();
                    })
                    .catch(function (error) { 
                        console.log(error); 
                    });
                }
            },
            updateStore: function(id, title, desc, slug, language_id ){
                var vm = this;
                vm.item_type == 'languages' ? language_id = true : language_id;
                vm.waitingResponse = "Waiting for response.";
                if(title && slug && language_id){
                    axios.post(this.ctrlURL+id+'?page='+vm.currentPage, { 
                        id: id,
                        name: title, 
                        desc: desc, 
                        slug: slug,
                        language_id: language_id,
                        orderBy: vm.itemOrder.type,
                        orderDir: vm.itemOrder.direction,
                        _token: vm.token,
                        _method: "PUT"
                    }) .then(function (response) { 
                        vm.items = response.data.items;
                        vm.updateSlugEdit = false;
                        vm.fieldEditMode = null;
                        console.log(response);
                        vm.addingItemReInit();
                        vm.waitingResponse = "S";
                    })
                    .catch(function (error) { 
                        console.log(error); 
                    });
                }
            },
            deleteItem: function(item){
                var vm = this;

                vm.waitingResponse = "Waiting for response.";
                axios.post(this.ctrlURL+item.id, { 
                    id: item.id,
                    orderBy: vm.itemOrder.type,
                    orderDir: vm.itemOrder.direction,
                    _token: vm.token,
                    _method: "DELETE",
                }) .then(function (response) { 
                    if( response.data.error_message ){
                        vm.errorLangReq = true;
                    }
                    else{
                        vm.errorLangReq = false;
                    }
                    vm.items = response.data.items;
                    console.log(response);
                    vm.id = response.data.maxCol+1;
                    vm.idColMax = response.data.maxCol+1;
                    vm.addingItemReInit();
                })
                .catch(function (error) { 
                    console.log(error); 
                });
                
            },
            deleteItemBulk: function(){
                var vm = this;
                vm.waitingResponse = "Waiting for response.";
                axios.post(this.ctrlURL+'delete', { 
                    ids: vm.bulkEditing,
                    action: 'deleteBulk',
                    orderBy: vm.itemOrder.type,
                    orderDir: vm.itemOrder.direction,
                    _token: vm.token,
                    _method: "DELETE",
                }) .then(function (response) { 
                    vm.items = response.data.items;
                    vm.addingItemReInit();
                })
                .catch(function (error) { 
                    console.log(error); 
                });
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
                for (var i=0; i<this.items.length; i++ ) {
                    if(this.items[i]){
                        outS.push(this.items[i].slug);
                        outN.push(this.items[i].name);
                        outD.push(this.items[i].description);
                        outL.push(this.items[i].language_id);
                    }
                }
                this.slugEdit = outS;
                this.titleEdit = outN;
                this.descEdit = outD;
                this.language_idEdit = outL;
            }
        },
        watch: {
            slug: function () { 
                if(this.item_type == 'languages'){
                    var string = this.slug;
                    var length = 2;
                    this.slug = string.substring(0, length);
                }
            }
        },
        created: function () { 
            var vm =this;
            vm.addingItemReInit();
        }
    });
</script>
@endsection