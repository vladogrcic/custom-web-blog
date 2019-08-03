@extends('layouts.manage', ['title_text' => '<i class="fa fa-cogs"></i>', 'item_type' => 'Settings', 'customButton' => 
'<button class="button is-success is-pulled-right" :class="{ \'is-loading\': sending, \'is-success\': edited, \'is-hovered\': !edited }" @click="updateStore()" :disabled="!edited"><i class="fa fa-save"></i><span class="m-l-15">Save</span></button>'
])
@section('content')

    <div class="card">
        <div class="card-content">
            <div style="display: none;" v-show="!isSiteLoading">
                <b-tabs v-model="activeTab">
                    <b-tab-item label="General">
                        <div class="columns">
                            <div class="column">
                                <div class="avatar-image" :style="{ backgroundImage: 'url('+previewFile+')'}"><button class="button is-danger" @click="deleteFavicon()"><i class="fa fa-trash" aria-hidden="true"></i></button> </div>
                            </div>
                            <div class="column">
                                <div>
                                    <b-upload v-model="dropProfPic"
                                        {{-- multiple --}}
                                        drag-drop @change="previewFileCalc()">
                                        <section class="section">
                                            <div class="content has-text-centered">
                                                <p>
                                                    <b-icon
                                                        pack="fa"
                                                        icon="upload"
                                                        size="is-large">
                                                    </b-icon>
                                                </p>
                                                <p>Drop your files here or click to upload</p>
                                            </div>
                                        </section>
                                    </b-upload>
                                </div>
                                
                            </div>
                        </div>
                        <div class="m-t-15">
                            <h5>Title</h5>
                            <input type="text" name="title" class="input" v-model="info.title">
                        </div>
                        <div class="m-t-15">
                            <h5>Description</h5>
                            <input type="text" name="description" class="input" v-model="info.description">
                        </div>
                        <div class="m-t-15">
                            <h5>Site Address</h5>
                            <input type="text" name="site_address" class="input" v-model="info.site_address">
                        </div>
                    </b-tab-item>
                    
                    <b-tab-item label="Front-end">
                        <div class="columns">
                            <div class="column">
                                    <div class="m-t-15">
                                        <h5>Post Permalink Format</h5>
                                        <div class="m-l-35">
                                            <label class="radio" style="width: 100%;">
                                                <div class="columns" style="width: 100%;">
                                                    <div class="column is-narrow"><input type="radio" name="perma_format" value="Y/m/d/slug" v-model="info.perma_format"></div>
                                                    <div class="column">https://www.sample.com/2019/03/23/sample-post/</div></div>
                                            </label><br>
                                            <label class="radio" style="width: 100%;">
                                                <div class="columns" style="width: 100%;">
                                                    <div class="column is-narrow"><input type="radio" name="perma_format" value="Y/m/slug" v-model="info.perma_format"></div>
                                                    <div class="column">https://www.sample.com/2019/03/sample-post/</div></div>
                                            </label><br>
                                            <label class="radio" style="width: 100%;">
                                                <div class="columns" style="width: 100%;">
                                                    <div class="column is-narrow"><input type="radio" name="perma_format" value="slug" v-model="info.perma_format"></div>
                                                    <div class="column">https://www.sample.com/sample-post/</div></div>
                                            </label><br>
                                        </div>
                                    </div>
                                <div class="m-t-15">
                                    <h5>Date Format</h5>
                                    <div class="m-l-35">
                                        <label class="radio">
                                            <input type="radio" name="date_format" value="F j, Y" v-model="info.date_format">
                                            March 13, 2019
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="date_format" value="j F, Y" v-model="info.date_format">
                                            13 March, 2019
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="date_format" value="Y-m-d" v-model="info.date_format">
                                            2019-03-13
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="date_format" value="m/d/Y" v-model="info.date_format">
                                            03/13/2019
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="date_format" value="d/m/Y" v-model="info.date_format">
                                            13/03/2019
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="date_format" value="n/j/Y" v-model="info.date_format">
                                            3/13/2019
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="date_format" value="j/n/Y" v-model="info.date_format">
                                            13/3/2019
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="date_format" value="d.m.Y" v-model="info.date_format">
                                            13.03.2019
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="date_format" value="m.d.Y" v-model="info.date_format">
                                            03.13.2019
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="date_format" value="j.n.Y" v-model="info.date_format">
                                            13.3.2019
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="date_format" value="n.j.Y" v-model="info.date_format">
                                            3.13.2019
                                        </label>
                                    </div>
                                </div>
                                <div class="m-t-15">
                                    <h5>Time Format</h5>
                                    <div class="m-l-35">
                                        <label class="radio">
                                            <input type="radio" name="time_format" value="g:i a" v-model="info.time_format">
                                            6:58 am
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="time_format" value="h:i a" v-model="info.time_format">
                                            06:58 am
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="time_format" value="g:i A" v-model="info.time_format">
                                            6:58 AM
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="time_format" value="h:i A" v-model="info.time_format">
                                            06:58 AM
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="time_format" value="H:i" v-model="info.time_format">
                                            06:58 or 18:58
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="time_format" value="G:i" v-model="info.time_format">
                                            6:58 or 18:58
                                        </label><br>
                                        <label class="radio">
                                            <input type="radio" name="time_format" value="g:i" v-model="info.time_format">
                                            6:58
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-4">
                                <div class="m-t-15">
                                    <label class="checkbox">
                                        <input type="checkbox" name="disable_language_group" v-model="info.disable_language_group">
                                        Disable language grouping
                                    </label>
                                </div>
                                <div class="m-t-15">
                                    <h5>Main language</h5>
                                    <div class="select">
                                    <select name="main_lang" id="main_lang" v-model="info.main_lang">
                                        @foreach ($languages as $language)
                                            <option value="{{$language->id}}" selected>{{$language->name}}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                                <div class="m-t-15">
                                    <h5>Number of posts per page</h5>
                                    <input type="number" name="per_page" class="input" v-model="info.per_page">
                                </div>
                                <div class="m-t-15">
                                    <h5>Show language as</h5>
                                    <div class="select">
                                        <select name="show_lang_switch" id="show_lang_switch" v-model="info.show_lang_switch">
                                            <option value="icons" selected>Icons</option>
                                            <option value="slugs">Slugs</option>
                                            <option value="iconsSlugs">Icons and Slugs</option>
                                            <option value="fullName">Full Language Name</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="m-t-15">
                                    <h5>Blog url slug</h5>
                                    <input type="text" name="blog_slug" class="input" v-model="info.blog_slug" placeholder="blog">
                                </div>
                            </div>
                        </div>
                    </b-tab-item>
                    <b-tab-item label="Back-end">
                        <div class="m-t-15">
                            <h5>Timezone</h5>
                            <div class="select">
                                <select name="timezone" v-model="info.timezone">
                                    <optgroup label="Europe">
                                        @for($i=0;$i<count($timeZoneGroups['EUROPE']);$i++)
                                            <option value="{{$timeZoneGroups['EUROPE'][$i]}}">{{$timeZoneGroups['EUROPE'][$i]}}</option>
                                        @endfor
                                    </optgroup>
                                    <optgroup label="America">
                                        @for($i=0;$i<count($timeZoneGroups['AMERICA']);$i++)
                                            <option value="{{$timeZoneGroups['AMERICA'][$i]}}">{{$timeZoneGroups['AMERICA'][$i]}}</option>
                                        @endfor
                                    </optgroup>
                                    <optgroup label="Australia">
                                        @for($i=0;$i<count($timeZoneGroups['AUSTRALIA']);$i++)
                                            <option value="{{$timeZoneGroups['AUSTRALIA'][$i]}}">{{$timeZoneGroups['AUSTRALIA'][$i]}}</option>
                                        @endfor
                                    </optgroup>
                                    <optgroup label="Africa">
                                        @for($i=0;$i<count($timeZoneGroups['AFRICA']);$i++)
                                            <option value="{{$timeZoneGroups['AFRICA'][$i]}}">{{$timeZoneGroups['AFRICA'][$i]}}</option>
                                        @endfor
                                    </optgroup>
                                    <optgroup label="Antarctica">
                                        @for($i=0;$i<count($timeZoneGroups['ANTARCTICA']);$i++)
                                            <option value="{{$timeZoneGroups['ANTARCTICA'][$i]}}">{{$timeZoneGroups['ANTARCTICA'][$i]}}</option>
                                        @endfor
                                    </optgroup>
                                    <optgroup label="Asia">
                                        @for($i=0;$i<count($timeZoneGroups['ASIA']);$i++)
                                            <option value="{{$timeZoneGroups['ASIA'][$i]}}">{{$timeZoneGroups['ASIA'][$i]}}</option>
                                        @endfor
                                    </optgroup>
                                    <optgroup label="Atlantic">
                                        @for($i=0;$i<count($timeZoneGroups['ATLANTIC']);$i++)
                                            <option value="{{$timeZoneGroups['ATLANTIC'][$i]}}">{{$timeZoneGroups['ATLANTIC'][$i]}}</option>
                                        @endfor
                                    </optgroup>
                                    <optgroup label="Pacific">
                                        @for($i=0;$i<count($timeZoneGroups['PACIFIC']);$i++)
                                            <option value="{{$timeZoneGroups['PACIFIC'][$i]}}">{{$timeZoneGroups['PACIFIC'][$i]}}</option>
                                        @endfor
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </b-tab-item>
                </b-tabs>
            </div>
        </div>
    </div> <!-- end of .card -->
    
@endsection

@section('scripts')
    <script>
    var app = new Vue({
        el: '#app',
        data: {
            isSiteLoading: false,
            api_token: '{{Auth::user()->api_token}}',
            token: "{{ csrf_token() }}",
            waitingResponse: "S",
            action: '{{ $items_url }}',
            activeTab: 0,
            edited: false,
            sending: false,
            editMode: false,
            previewFile: '{{url('/')}}/images/placeholder-250x250.png',
            favicon: '{{ $items["favicon"] }}',
            fieldEditMode: null,
            user: {{ Auth::user()->id }},
            info: {
                // 'favicon': '{{-- $items["favicon"] --}}',
                'title': '{{ $items["title"] }}',
                'description': '{{ $items["description"] }}',
                'site_address': '{{ $items["site_address"] }}',
                'timezone': "{{ $items['timezone'] }}",
                'date_format': '{{ $items["date_format"] }}',
                'time_format': '{{ $items["time_format"] }}',
                'perma_format': '{{ $items["perma_format"] }}',
                'disable_language_group': '{{ $items["disable_language_group"] }}',
                'per_page': '{{ $items["per_page"] }}',
                'show_lang_switch': '{{ $items["show_lang_switch"] }}',
                'main_lang': '{{ $items["main_lang"] }}',
                'blog_slug': '{{ $items["blog_slug"] }}',
            },
            dropProfPic: null,
            favicon: '{{ $items["favicon"] }}',
            delete_favicon: false,
            currentPage: 1,
            ctrlURL: '/manage/settings/',
            item_type: 'settings',
            itemOrder: {
                type: 'id',
                direction: 'desc'
            }
        },
        methods:{
            updateStore: function(){
                var vm = this;
                vm.sending = true;
                vm.waitingResponse = "Waiting for response.";
                var formData = new FormData();
                if(vm.dropProfPic){
                    formData.append("file", vm.dropProfPic);
                    formData.append('_token', vm.token);
                }
                axios.post(siteUrl+vm.ctrlURL+vm.user, { 
                    info: vm.info,
                    action:'updateSettings',
                    _token: vm.token,
                    _method: "PUT"
                }) .then(function (response) { 
                    if(response.status == 200){
                        vm.sending = false;
                        console.log('Success');
                        vm.edited = false;
                    }
                })
                .catch(function (error) { 
                    console.log(error); 
                });
                if(vm.delete_favicon){
                    formData.append('action','deleteFavicon');
                }
                else{
                    formData.append('action','uploadFavicon');
                }
                if(vm.delete_favicon || vm.dropProfPic){
                    axios({
                        method: 'post',
                        url: '/manage/upload/favicon',
                        data: formData,
                    }).then(function (response) { 
                        if(response.status == 200){
                            console.log('Success');
                            vm.edited = false;
                            vm.delete_favicon = false;
                        }
                    });
                }
            },
            previewFileCalc: function () {
                var vm = this;
                var input = vm.dropProfPic;
                if (input) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        vm.favicon = e.target.result
                    }
                    reader.readAsDataURL(input);
                }
            },
            deleteFavicon: function () {
                var vm = this;
                vm.previewFile = '{{url('/')}}/images/placeholder-250x250.png';
                vm.favicon = '';
                vm.dropProfPic = null;
                vm.delete_favicon = true;
            },
        },
        watch: {
            info: {
                handler: function (val, oldVal) {
                    var vm = this;
                    vm.edited = true;
                    if(this.favicon) this.previewFile = this.favicon;
                }, deep:true
            },
            favicon: function (val,oldVal) {
                this.edited = true;
                if(this.favicon) this.previewFile = this.favicon;
            },
            dropProfPic: {
                handler: function (val,oldVal) {
                    var vm = this;
                    this.previewFileCalc();
                    vm.edited = true;
                }, deep:true
            },
        },
        created: function () {
            this.previewFileCalc();
            if(!this.favicon) this.previewFile = '{{url('/')}}/images/placeholder-250x250.png';
            else  this.previewFile = this.favicon;
        },
        mounted:function(){
            window.addEventListener("load", function(event) {
                this.isSiteLoading = false;
            });
        }  
    });
</script>
@endsection