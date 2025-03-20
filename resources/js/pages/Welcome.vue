<script setup lang="ts">
import { reactive, ref } from 'vue';
import axios from 'axios';

const form = reactive({
    email: '',
    status: 'subscribed',
});

const message = ref(null);
const errors = ref([]);

const createSubscriber = async () => {
    try {
        const response = await axios.post('/subscribers', form);

        message.value = response.data.message;
        errors.value = [];

        form.email = '';
        form.status = 'subscribed';
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            message.value = 'Please fix the errors above';
        } else if (error.response?.status === 429) {
            message.value = 'Too many requests, please wait a minute';
        } else {
            message.value = 'Error creating subscriber: ' + error;
        }
    }
};

</script>

<template>
    <div>
        <h2>Create Subscriber</h2>

        <form @submit.prevent="createSubscriber">
            <div>
                <input v-model="form.email" placeholder="Email" required />
                <p v-if="errors.email" style="color: red">{{ errors.email[0] }}</p>
            </div>

            <div>
                <select v-model="form.status" required>
                    <option value="">Please Select</option>
                    <option value="subscribed">Subscribed</option>
                    <option value="unsubscribed">Unsubscribed</option>
                </select>
                <p v-if="errors.status" style="color: red">{{ errors.status[0] }}</p>
            </div>

            <button type="submit">Create</button>
        </form>

        <p v-if="message">{{ message }}</p>
    </div>
</template>
