<style scoped>
  .slug-widget {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    height: 28px;
  }
  .wrapper {
    margin-left: 8px;
  }
  .icon-wrapper{
    width: 5%;
    float: right;
    /* position: absolute;
    right: 0; */
  }
  .button-wrapper{
    width: 20%;
    height: 27px;
    float: right;
    /* position: absolute;
    right: 0; */
  }
  .slug {
    background-color: #fdfd96;
    padding: 3px 5px
  }
  .url-wrapper span.slug
  {
    min-width: 50%;
  }
  .url-wrapper div,
  .url-wrapper div .input {
    width: 50%;
  }
  .url-wrapper {
    width: 75%;
    display: flex;
    align-items: center;
    height: 28px;
  }
</style>

<template>
  <div class="slug-widget">
    <div class="icon-wrapper wrapper">
      <i class="fa fa-link"></i>
    </div>
    <div class="url-wrapper wrapper">
      <span class="root-url">{{url}}</span>
      <span class="subdirectory-url">/{{subdirectory}}/</span>
      <span class="slug" v-show="slug && !isEditing">{{slug}}</span>
      <div><input type="text" name="slug" class="input is-small" v-show="isEditing" v-model="customSlug"/></div>
    </div>

    <div class="button-wrapper wrapper">
      <button class="save-slug-button button is-small" v-show="!isEditing" @click.prevent="editSlug">Edit</button>
      <button class="save-slug-button button is-small" v-show="isEditing" @click.prevent="saveSlug">Save</button>
      <button class="save-slug-button button is-small" v-show="isEditing" @click.prevent="resetEditing">Reset</button>
    </div>
  </div>
</template>

<script>
    export default {
      props: {
        url: {
          type: String,
          required: true
        },
        subdirectory: {
          type: String,
          required: true
        },
        title: {
          type: String,
          required: true
        }
      },
      data: function() {
        return {
          slug: this.convertTitle(),
          isEditing: false,
          customSlug: '',
          wasEdited: false
        }
      },
      methods: {
        convertTitle: function() {
          return Slug(this.title)
        },
        editSlug: function() {
          this.customSlug = this.slug;
          this.$emit('edit', this.slug);
          this.isEditing = true;
        },
        saveSlug: function() {
          // run ajax to see if new slug is unique
          if (this.customSlug !== this.slug) this.wasEdited = true;
          this.slug = Slug(this.customSlug);
          this.$emit('save', this.slug);
          this.isEditing = false;
        },
        resetEditing: function() {
          this.slug = this.convertTitle();
          this.$emit('reset', this.slug);
          this.wasEdited = false;
          this.isEditing = false;
        }
      },
      watch: {
        title: _.debounce(function() {
            if (this.wasEdited == false) this.slug = this.convertTitle()
            // run ajax to see if slug is unique
            // if not unique, customize the slug to make it unique
          }, 250),
        slug: function(val) {
          this.$emit('slug-changed', this.slug)
        }
      }
    }
</script>
