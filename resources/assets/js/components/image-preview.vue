<style scoped>
</style>

<template>
    <div class="columns has-background-white is-gapless">
                <div class="column is-three-fifths" style="width: 65%;">
                    <div class="image" style="height: 100%; border-left: solid 1px #4c4c4c; border-top: solid 1px #4c4c4c; border-bottom: solid 1px #4c4c4c; ">
                        <div :style="{ backgroundImage: 'url('+siteUrl+origImageUrl+imgviewprop['core']+')' }" style="height: 100%; background-size: contain; background-repeat: no-repeat; background-position: center; background-color: #323232; min-height:350px;"></div>

                    </div>
                </div>
                <div class="column" style="max-width: 35%;">
                    <section class="hero is-small is-info is-bold">
                        <div class="hero-body">
                            <div class="container">
                                <h1 class="title is-5">
                                    File Details
                                </h1>
                                
                            </div>
                        </div>
                    </section>
                    <div class ="p-t-15 p-l-15 p-r-15">
                        <section class="rowed-column" v-if="imgviewprop['exif'] !== 'undefined'">
                            <div class="columns">
                                <div class="column">
                                    <span class="subtitle is-5">Basic Details</span>
                                </div>
                            </div>
                            <div class="columns">
                                <div class="column is-narrow icon"><i class="fa fa-info"></i></div>
                                <div class="column">{{imgviewprop['name']}}</div>
                            
                                <div class="column is-narrow icon"><i class="fa fa-file"></i></div>
                                <div class="column">{{imgviewprop['mimeType']}}</div><br>
                            </div>
                            <div class="columns">
                                <div class="column is-narrow icon"><i class="fa fa-calendar"></i></div>
                                <div class="column">{{imgviewprop['modified']['date']}}</div><br>
                                <div class="column is-narrow icon"><i class="fa fa-clock-o"></i></div>
                                <div class="column">{{imgviewprop['modified']['time']}}</div><br>
                            </div>
                            <div class="columns">
                                <div class="column is-narrow icon"><i class="fa fa-eye"></i></div>
                                <div class="column">{{imgviewprop['resolution']['width']+ ' x ' +imgviewprop['resolution']['height']}}</div>
                                <div class="column is-narrow icon"><i class="fa fa-hdd-o"></i></div>
                                <div class="column">{{imgviewprop['size']}}</div>
                            </div>
                            <div class="column-part-2" v-if="Object.keys(imgviewprop['exif']).length !== 0">
                                <div class="columns">
                                    <div class="column">
                                        <span class="subtitle is-5">Camera Details</span>
                                    </div>
                                </div>
                                <div class="columns" v-if="imgviewprop['exif'].hasOwnProperty('DateTimeOriginal')">
                                    <div class="is-bold column is-one-half">Date Taken: </div>
                                    <div class="column" v-if="imgviewprop['exif']['DateTimeOriginal'].hasOwnProperty('date')">{{imgviewprop['exif']['DateTimeOriginal']['date']}}</div>
                                </div>
                                <div class="columns" v-if="imgviewprop['exif'].hasOwnProperty('DateTimeOriginal')">
                                    <div class="is-bold column is-one-half">Time Taken: </div>
                                    <div class="column" v-if="imgviewprop['exif']['DateTimeOriginal'].hasOwnProperty('time')">{{imgviewprop['exif']['DateTimeOriginal']['time']}}</div>
                                </div>
                                <div class="columns">
                                    <div class="is-bold column is-one-half">Camera Maker: </div>
                                    <div class="column" v-if="imgviewprop['exif'].hasOwnProperty('Make')">{{imgviewprop['exif']['Make']}}</div><br>
                                </div>
                                <div class="columns">
                                    <div class="is-bold column is-one-half">Camera Model: </div>
                                    <div class="column" v-if="imgviewprop['exif'].hasOwnProperty('Model')">{{imgviewprop['exif']['Model']}}</div><br>
                                </div>
                                <div class="columns">
                                    <div class="is-bold column is-one-half">Exposure Time: </div>
                                    <div class="column" v-if="imgviewprop['exif'].hasOwnProperty('ExposureTime')">{{imgviewprop['exif']['ExposureTime']}}</div><br>
                                </div>
                                <div class="columns">
                                    <div class="is-bold column is-one-half">F Number: </div>
                                    <div class="column" v-if="imgviewprop['exif'].hasOwnProperty('FNumber')">{{imgviewprop['exif']['FNumber']}}</div><br>
                                </div>
                                <div class="columns">
                                    <div class="is-bold column is-one-half">Focal Length: </div>
                                    <div class="column" v-if="imgviewprop['exif'].hasOwnProperty('FocalLength')">{{calcNum(imgviewprop['exif']['FocalLength'])+' mm'}}</div><br>
                                </div>
                                <div class="columns" v-if="imgviewprop['exif'].hasOwnProperty('COMPUTED')">
                                    <div class="is-bold column is-one-half">Aperture F Number:</div>
                                    <div class="column" v-if="imgviewprop['exif']['COMPUTED'].hasOwnProperty('ApertureFNumber')">{{imgviewprop['exif']['COMPUTED']['ApertureFNumber']}}</div><br>
                                </div>  
                                <div class="columns" v-if="imgviewprop['exif'].hasOwnProperty('COMPUTED')">
                                    <div class="is-bold column is-one-half">Copyright:</div>
                                    <div class="column" v-if="imgviewprop['exif']['COMPUTED'].hasOwnProperty('Copyright')">{{imgviewprop['exif']['COMPUTED']['Copyright']}}</div><br>
                                </div>  
                            </div>  
                        </section>  
                    </div>
                </div>
                
            </div>
</template>

<script>
    export default {
      props: {
        imgviewprop: {
          type: Object,
          default: function() {
            return {
              core: "",
              thumb: "",
              name: "",
              mimeType: "",
              resolution: {
                width: "",
                height: ""
              },
              modified: {
                date: "",
                time: ""
              },
              size: "",
              exif: {
                DateTimeOriginal: {
                  date: "",
                  time: ""
                },
                FocalLength: "",
                FNumber: "",
                ExposureTime: "",
                Model: "",
                Make: "",
                COMPUTED: {
                  ApertureFNumber: ""
                }
              }
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
          initFileProp: {},
          activeData: false,
          siteUrl: siteUrl?siteUrl:'',
          origImageUrl: origImageUrl?origImageUrl:''
        };
      },
      created: function() {
          console.log(this.imgviewprop['exif'].hasOwnProperty('DateTimeOriginal'));
      },
      methods: {
          calcNum: function(input){
            input = input.split("/");
            return parseInt(input[0])/parseInt(input[1]);
          }
      }
    };
</script>
