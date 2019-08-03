@extends('layouts.manage', ['title_text' => '<i class="fa fa-address-card"></i>', 'item_type' => 'Profile', 'customButton' => 
'<button class="button is-success is-pulled-right" :class="{ \'is-loading\': sending, \'is-success\': edited, \'is-hovered\': !edited }" @click="updateStore()" :disabled="!edited"><i class="fa fa-save"></i><span class="m-l-15">Save</span></button>'
])
@section('content')

    <div class="card">
        <div class="card-content" style="display: none;" v-show="!isSiteLoading">
                <table class="table">
                        <tr>
                            <th>Username</th>
                            <th>Member Since</th> 
                        </tr>
                        <tr>
                            <td>{{$user->display_name}}</td>
                            <td>{{ date("d.m.Y H:i:s", strtotime($user->created_at)) }}</td> 
                        </tr>
                    </table>
            <b-tabs v-model="activeTab">
                <b-tab-item label="Avatar">
                    <div class="columns">
                        <div class="column">
                            <div class="avatar-image" :style="{ backgroundImage: 'url('+previewFile+')'}"><button class="button is-danger" @click="deleteAvatar()"><i class="fa fa-trash" aria-hidden="true"></i></button> </div>
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
                            <div class="gravatar-wrapper m-t-50">
                                <input type="checkbox" name="gravatar" id="gravatar" {{ $items['gravatar']?'checked':'' }} :checked="info.gravatar" v-model="info.gravatar">
                                <label for="gravatar">Enable Gravatar.</label>
                            </div>
                        </div>
                    </div>
                </b-tab-item>
                <b-tab-item label="General">
                    <div>
                        <h4>First Name</h4>
                    <input type="text" name="first_name" class="input" value="{{ $items['first_name'] }}" v-model="info.first_name">
                    </div>
                    <div>
                        <h4>Last Name</h4>
                        <input type="text" name="last_name" class="input" value="{{ $items['last_name'] }}"  v-model="info.last_name">
                    </div>
                    <div>
                        <h4>Gender</h4>
                        <select name="gender" id="gender" class="input" value="{{ $items['gender'] }}" v-model="info.gender">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="m-b-50">
                        <h4>Birthday</h4>
                        <b-datepicker 
                            name="birthday"
                            placeholder="Click to select..."
                            v-model="birthday"
                            position="is-top-right"
                            icon-pack="fa"
                            :date-formatter="(date) => date.toLocaleDateString('de-DE')"
                            icon="calendar-today">
                        </b-datepicker>
                        
                    </div>
                </b-tab-item>
                <b-tab-item label="Account Info">
                    <div>
                        <h4>Username</h4>
                        <input type="text" name="username" class="input" value="{{ $items['username'] }}" v-model="info.username">
                    </div>
                    <div>
                        <h4>Display Name</h4>
                        <input type="text" name="displayname" class="input" value="{{ $items['displayname'] }}" v-model="info.displayname">
                    </div>
                    <div>
                        <h4>Email Address</h4>
                        <input type="email" name="email" class="input" value="{{ $items['email'] }}" v-model="info.email">
                    </div>
                    <div>
                        <h4>New Password</h4>
                        <input type="password" name="password" class="input" v-model="info.password">
                    </div>
                </b-tab-item>
                <b-tab-item label="Contact Info">
                    <div>
                        <h4>Mobile Phone</h4>
                        <input type="text" name="mobile_phone" class="input" value="{{ $items['mobile_phone'] }}" v-model="info['mobile_phone']">
                    </div>
                    <div>
                        <h4>Address</h4>
                        <input type="text" name="address" class="input" value="{{ $items['address'] }}" v-model="info.address">
                    </div>
                    <div>
                        <h4>City</h4>
                        <input type="text" name="city" class="input" value="{{ $items['city'] }}" v-model="info.city">
                    </div>
                    <div>
                        <h4>Country</h4>
                        <input type="text" name="country" class="input" value="{{ $items['country'] }}" v-model="info.country">
                    </div>
                </b-tab-item>
            </b-tabs>
        </div>
    </div>
    
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
            edited: false,
            sending: false,
            editMode: false,
            activeTab: null,
            previewFile: '{{url('/')}}/images/placeholder-250x250.png',
            fieldEditMode: null,
            user: {{ Auth::user()->id }},
                        birthday: new Date('1993-12-23'),
            info: {
                'gravatar': '{{ $items["gravatar"] }}',
                'first_name': '{{ $items["first_name"] }}',
                'last_name': '{{ $items["last_name"] }}',
                'username': '{{ $items["username"] }}',
                'displayname': '{{ $items["displayname"] }}',
                'email': '{{ $items["email"] }}',
                'password': '',
                'gender': '{{ $items["gender"] }}',
                'birthday': "{{ $items['birthday'] }}",
                'mobile_phone': '{{ $items["mobile_phone"] }}',
                'address': '{{ $items["address"] }}',
                'city': '{{ $items["city"] }}',
                'country': '{{ $items["country"] }}',
            },
            dropProfPic: null,
            avatar: '{{ $items["avatar"] }}',
            delete_avatar: false,
            birthday: new Date('1993-12-23'),
            currentPage: 1,
            ctrlURL: '/manage/profile/',
            item_type: 'profile',
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
                    action:'updateProfile',
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
                if(vm.delete_avatar){
                    formData.append('action','deleteAvatar');
                }
                else{
                    formData.append('action','uploadAvatar');
                }
                if(vm.delete_avatar || vm.dropProfPic){
                    axios({
                        method: 'post',
                        url: '/manage/upload/avatar',
                        data: formData,
                    }).then(function (response) { 
                        if(response.status == 200){
                            console.log('Success');
                            vm.edited = false;
                            vm.delete_avatar = false;
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
                        vm.avatar = e.target.result
                    }
                    reader.readAsDataURL(input);
                }
            },
            deleteAvatar: function () {
                var vm = this;
                vm.previewFile = '{{url('/')}}/images/placeholder-250x250.png';
                vm.avatar = '';
                vm.dropProfPic = null;
                vm.delete_avatar = true;
            },
        },
        computed:{
            birthdayOut: function (params) {
                return this.birthday.toLocaleDateString('de-DE');
            }
        },
        watch: {
            info: {
                handler: function (val, oldVal) {
                    var vm = this;
                    vm.edited = true;
                    if(this.avatar) this.previewFile = this.avatar;
                }, deep:true
            },
            birthday: function (val,oldVal) {
                var vm = this;
                if (vm.birthday.getMonth) {
                    var value = '';
                    var year = vm.birthday.getFullYear();
                    var month = vm.birthday.getMonth();
                    var date = vm.birthday.getDate();
                    var hours = vm.birthday.getHours();
                    var minutes = vm.birthday.getMinutes();
                    var seconds = vm.birthday.getSeconds();
                    value = year+'-'+month+'-'+date;
                    vm.info['birthday'] = value;
                }
            },
            avatar: function (val,oldVal) {
                this.edited = true;
                if(this.avatar) this.previewFile = this.avatar;
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
            if(!this.avatar) this.previewFile = '{{url('/')}}/images/placeholder-250x250.png';
            else  this.previewFile = this.avatar;
        },
        mounted:function(){
            window.addEventListener("load", function(event) {
                this.isSiteLoading = false;
            });
        }  
    });
</script>
@endsection