import axios from "axios";

export default {
    data() {
        return {
            Status: '',
        };
    },
    created() {
        axios.get('/user-status')
            .then(response => {
                this.Status = response.data.user.status;
            })
            .catch(error => {
                console.error('Error fetching user role:', error);
            });
    }
};
