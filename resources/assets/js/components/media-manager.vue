<style scoped>
</style>

<template>
<div id="mainMedia">
    <div class="media-manager-wrapper" style="position: relative;">
        <span class="crumbs">
            <span class="icon is-medium" @click="handleFileList('', 'crumbs', true)"><i class="fa fa-home"></i></span> /
            <span @click="handleFileList(index, 'crumbs')" v-for="(item, index) in initFileProp['folderTree']" v-if="item" v-html="item+' / '" :key="item">/</span>
        </span>
        <hr>
        <div class="media-manager columns is-multiline">
            <div class="column is-one-quarter folder" v-for="(item, index) in initFileProp['folders']" :key="item">

                <div class="card" style="position:relative;">
                    <div class="card-image" @click.stop="handleFileList(index)">

                        <figure class="image">
                            <div v-if="initFileProp['folderProps'][index]['number'] || initFileProp['folderProps'][index]['files']" :style="{ backgroundImage: 'url('+siteUrl+'/images/folder.svg)' }"></div>
                            <div v-else :style="{ backgroundImage: 'url('+siteUrl+'/images/folder-empty.svg)' }"></div>

                        </figure>

                    </div>
                    <div class="card-content">
                        <div class="media">
                            <div class="media-content">
                                <input v-show="editable" type="checkbox" style="width: 25px; height: 25px; position: absolute; z-index:99999; top:10px; left:10px;"
                                    v-model="deleteBulkFolder[index]">
                                <span class="icon title is-6"><i class="fa fa-folder"></i></span>
                                <span class="subtitle is-6">{{ initFileProp['folderProps'][index]['name'] }}</span>
                                <div class="media-sub-content" style="">
                                    <div>
                                        <span class="icon title is-6"><i class="fa fa-folder-open"></i></span>
                                        <span class="subtitle is-6">{{ initFileProp['folderProps'][index]['number']}}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="icon title is-6"><i class="fa fa-files-o"></i></span>
                                        <span class="subtitle is-6">{{ initFileProp['folderProps'][index]['files'] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="media-sub-content" style="">
                                    <div class="" style="">
                                        <span class="icon title is-6"><i class="fa fa-calendar"></i></span>
                                        <time class="subtitle is-7">{{ initFileProp['folderProps'][index]['modified']['date']
                                            }}
                                        </time>
                                    </div>
                                    <div class="" style="">
                                        <span class="icon title is-6"><i class="fa fa-clock-o"></i></span>
                                        <time class="subtitle is-7">{{ initFileProp['folderProps'][index]['modified']['time']
                                            }}
                                        </time>
                                    </div>
                                </div>


                                <div class="" style="height: 28px; display: inline-block;">
                                    <span class="icon title is-7"><i class="fa fa-hdd-o"></i></span>
                                    <span class="subtitle is-7">{{ initFileProp['folderProps'][index]['size'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="content">
                        </div>
                    </div>

                </div>
            </div>
            <div class="column is-3 file" v-for="(item, index) in initFileProp['files']"  :key="item">
                <div class="card">
                    <div class="card-image">
                        <figure class="image" @click="featuredImageUrl(index)">
                            <div :style="{ backgroundImage:  'url('+siteUrl+thumb360+initFileProp['fileProps'][index]['coreUrl']+')' }">
                            </div>
                        </figure>
                    </div>
                    <div class="card-content">
                        <div class="media">
                            <div class="media-content">
                                <div class="filename" style="">
                                    <div class="" style="">
                                        <span class="icon title is-7"><i class="fa fa-info"></i></span>
                                        <span class="subtitle is-7">{{initFileProp['fileProps'][index]['name']}}</span><br>
                                    </div>
                                </div>
                                <div class="media-sub-content" style="">
                                    <div class="" style="">
                                        <span class="icon title is-7"><i class="fa fa-file"></i></span>
                                        <span class="subtitle is-7" style="">{{initFileProp['fileProps'][index]['mimeType']}}</span>
                                    </div>
                                    <div class="" style="">
                                        <span class="icon title is-7"><i class="fa fa-eye"></i></span>
                                        <span class="subtitle is-7">{{initFileProp['fileProps'][index]['resolution']['width']}}
                                            X {{initFileProp['fileProps'][index]['resolution']['height']}}</span>

                                    </div>
                                </div>
                                <div class="media-sub-content" style="">
                                    <div class="" style="">
                                        <span class="icon title is-6"><i class="fa fa-calendar"></i></span>
                                        <time class="subtitle is-7">{{initFileProp['fileProps'][index]['modified']['date']}}</time>
                                    </div>
                                    <div class="" style="">
                                        <span class="icon title is-6"><i class="fa fa-clock-o"></i></span>
                                        <time class="subtitle is-7">{{initFileProp['fileProps'][index]['modified']['time']}}</time>
                                    </div>
                                </div>

                                <div class="media-sub-content">
                                    <div>
                                        <span class="icon title is-7"><i class="fa fa-hdd-o"></i></span>
                                        <span class="subtitle is-7">{{initFileProp['fileProps'][index]['size']}}</span>
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
</template>

<script>
  export default {
    props: {
      formurl: {
        type: String,
        default: ''
      },
      imageurl: {
        type: Object,
        default: function() {
          return {
            core: "",
            thumb: "",
            name: "",
            mimeType: "",
            resolution: {},
            modified: {},
            size: ""
          };
        }
      }
    },
    model: {
      prop: "hidden",
      event: "input"
    },
    data: function() {
      return {
        dropFiles: [],
        editable: false,
        isCardModalUploadActive: false,
        isCardModalNewFolderActive: false,
        files: [],
        initFileProp: {},
        isLoading: false,
        isFullPage: false,
        addNewFolder: false,
        newFolder: "",
        iconDeleteBackHover: null,
        deleteBulkFolder: [],
        deleteBulk: [],
        content: this.value,
        siteUrl: siteUrl?siteUrl:'',
        thumb360: thumb360ImageUrl?thumb360ImageUrl:''
        //   imageUrl: ""
      };
    },
    created: function() {
      var image = [];
      for (let i = 0; i < this.initFileProp["allFiles"].length; i++) {
        image[i] = new Image();

        image[i].src = baseUrl + this.initFileProp["allFiles"][i];
      }
    },
    methods: {
      featuredImageUrl(index) {
        Vue.set(
          this.imageurl,
          "thumb",
          window.baseUrl + this.initFileProp["fileProps"][index]["thumbUrl"]
        );
        Vue.set(
          this.imageurl,
          "core",
          this.initFileProp["fileProps"][index]["coreUrl"]
        );
        Vue.set(
          this.imageurl,
          "mimeType",
          this.initFileProp["fileProps"][index]["mimeType"]
        );
        Vue.set(
          this.imageurl,
          "resolution",
          this.initFileProp["fileProps"][index]["resolution"]
        );
        Vue.set(
          this.imageurl,
          "modified",
          this.initFileProp["fileProps"][index]["modified"]
        );
        Vue.set(
          this.imageurl,
          "size",
          this.initFileProp["fileProps"][index]["size"]
        );
        Vue.set(
          this.imageurl,
          "name",
          this.initFileProp["fileProps"][index]["name"]
        );
        Vue.set(
          this.imageurl,
          "exif",
          this.initFileProp["fileProps"][index]["exif"]
        );
      },
      handleInput(e) {
        this.$emit("input", this.content);
      },
      slug(input) {
        return Slug(input);
      },
      openLoading(input) {
        this.isLoading ? (this.isLoading = false) : (this.isLoading = true);
        input == "full" ? (this.isFullPage = true) : (this.isFullPage = false);
      },
      submitNewFolder() {
        var vm = this,
          name = vm.initFileProp.folderTree.join("/");
        name.slice(-1) != "/" ? (name += "/") : name;
        name += vm.slug(vm.newFolder);
        vm.openLoading();
        axios
          .post(vm.formurl, {
            action: "createFolder",
            url: vm.initFileProp.folderTree.join("/"),
            name: name,
            // _token: "{{ csrf_token() }}",
            _method: "POST"
          })
          .then(function(response) {
            vm.initFileProp = response.data;
            vm.addNewFolder = false;
            vm.editable = false;
            vm.newFolder = "";
            vm.openLoading();
          })
          .catch(function(error) {
            console.log(error);
          });
      },
      deleteItem(index, type, fileType) {
        var vm = this,
          action,
          items = [],
          itemsFolder = [],
          name = vm.initFileProp.folderTree.join("/"),
          bulk;
        if (fileType == "file") {
          if (type == "delete") {
            action = "delete";
            bulk = name.slice(7);
            items.push(vm.initFileProp["files"][index]);
          } else {
            action = "delete";
            bulk = name.slice(7);
            for (var i = 0; i < vm.deleteBulk.length; i++) {
              if (vm.deleteBulk[i] == true)
                items.push(vm.initFileProp["files"][i]);
            }
          }
        } else {
          if (fileType == "both") {
            if (type == "delete") {
              action = "delete";
              bulk = name.slice(7);
              items.push(vm.initFileProp["files"][index]);
            } else {
              action = "delete";
              bulk = name.slice(7);
              for (var i = 0; i < vm.deleteBulk.length; i++) {
                if (vm.deleteBulk[i] == true)
                  items.push(vm.initFileProp["files"][i]);
              }
            }
          }
          if (type == "delete") {
            action = "delete";
            bulk = name.slice(7);
            itemsFolder.push(vm.initFileProp["folders"][index]);
          } else {
            action = "delete";
            bulk = name.slice(7);
            for (var i = 0; i < vm.deleteBulkFolder.length; i++) {
              if (vm.deleteBulkFolder[i] == true) {
                name.slice(-1) != "/" ? (name += "/") : name;
                itemsFolder.push(vm.initFileProp["folders"][i]);
              }
            }
          }
        }
        if (!bulk) bulk = "public";
        name.slice(-1) != "/" ? (name += "/") : name;
        name += vm.newFolder;
        vm.openLoading();
        axios
          .post("/manage/media/" + bulk, {
            action: action,
            url: vm.initFileProp.folderTree.join("/"),
            items: items,
            itemsFolder: itemsFolder,
            name: name,
            // _token: "{{ csrf_token() }}",
            _method: "DELETE"
          })
          .then(function(response) {
            vm.initFileProp = response.data;
            vm.addNewFolder = false;
            vm.deleteBulk = [];
            vm.deleteBulkFolder = [];
            vm.newFolder = "";
            vm.openLoading();
          })
          .catch(function(error) {
            console.log(error);
          });
      },
      handleFileList(index, type, root) {
        console.log(index);
        if (this.editable) return;
        var location,
          array,
          vm = this;
        if (typeof type === "undefined") type = "";
        if (type === "crumbs") {
          array = vm.initFileProp["folderTree"].slice(0, index + 1);
          location = array.join("/");
        } else {
          if (!(Object.keys(vm.initFileProp).length === 0))
            location = vm.initFileProp["folders"][index];
          else location = "/";
        }
        if (root) location = "/";
        vm.openLoading();
        axios
          .post(vm.formurl, {
            url: location,
            // _token: "{{ csrf_token() }}",
            _method: "GET"
          })
          .then(function(response) {
            vm.initFileProp = response.data;
            vm.addNewFolder = false;
            vm.newFolder = "";
            vm.openLoading();
          })
          .catch(function(error) {
            console.log(error);
          });
      },
      deleteDropFile(index) {
        this.files.splice(index, 1);
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
          formData.append("files[" + i + "]", file, Slug(file.name));
        }
        // formData.append('editMode', 'editing');
        formData.append("action", "uploadFile");
        // formData.append("currentFolder", "{{--$folderUrl--}}");
        formData.append("url", vm.initFileProp.folderTree.join("/"));
        vm.openLoading();
        axios
          .post(vm.formurl, formData, {
            headers: {
              "Content-Type": "multipart/form-data"
            }
          })
          .then(function(response) {
            console.log("SUCCESS!!");
            var i = vm.files.length;
            do {
              vm.deleteDropFile(i - 1);
              i--;
            } while (i);
            vm.initFileProp = response.data;
            vm.addNewFolder = false;
            vm.newFolder = "";
            vm.editable = false;
            vm.openLoading("full");
          })
          .catch(function() {
            console.log("FAILURE!!");
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
    created: function(params) {
      this.handleFileList(0);
    },
    mounted: function() {
      this.handleFileList(0);
    },
    computed: {
      filesUrl: function() {
        var file,
          url = [];
        for (var i = 0; i < this.files.length; i++) {
          file = this.files[i];
          url.push(URL.createObjectURL(file));
        }
        return url;
      }
    },
    watch: {
      addNewFolder: function() {
        Vue.nextTick(function() {
          if (document.getElementById("new-folder"))
            document.getElementById("new-folder").scrollIntoView();
        });
        // location.href = "#new-folder";
      },
      newFolder: _.debounce(function(input) {
        this.newFolder = this.slug(input);
      }, 5000)
      // imageUrl: function(params) {
      //   this.$emit("input", params);
      // }
    }
  };
</script>
