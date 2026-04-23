<template>
 <!-- Button trigger modal -->
<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">
  Chat Now
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Chat With {{recevierid}} {{receviername}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form @submit.prevent ="sendMsg()">
      <div class="modal-body">
       <textarea class="form-control" v-model="form.msg" rows="3" placeholder="Type Your Message"></textarea>
       <!-- Corrected template structure below -->
       <span class="text-success" v-if ="succMessage.message">{{succMessage.message}}</span>
       <span class="text-danger" v-if ="errors.msg">{{errors.msg[0]}}</span>
      </div>
      <div class="modal-footer">
        
        <button type="submit" class="btn btn-primary">Send Message</button>
      </div>
    </form>
    </div>
  </div>
</div>
</template>

<script>
export default {
  props :['recevierid','receviername'],
  data(){
    return{
      form:{
        msg:"", // <--- ADDED COMMA HERE
        receiver_id : this.recevierid,
      }, // <--- ADDED COMMA HERE
      errors:{},
      succMessage:{},
    }
  },
  methods:{
    sendMsg(){
      // Note: 'axios' needs to be globally available or imported if using modules
      axios.post('/send-message',this.form)
      .then((res) =>{
        this.form.msg = "";
        this.succMessage = res.data;
        this.errors = {}; // Clear previous errors on success
        console.log(res.data);
      }).catch((err) =>{
        this.succMessage = {}; // Clear previous success message on error
        this.errors = err.response.data.errors;
      })
    }
  }
}
</script>

