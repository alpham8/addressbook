<template>
    <div class="container abook-app-wrap">
        <div class="row">
            <div class="col">
                <h1>Contact address book app</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <form method="post" enctype="multipart/form-data" v-on:submit.prevent="onNewAddressSubmitted" accept-charset="UTF-8">
                    <div class="form-group">
                        <label for="firstname">first name</label>
                        <input type="text" id="firstname" class="form-control" name="firstname" aria-describedby="firstnameHelp" />
                        <small id="firstnameHelp" class="form-text text-muted">
                            Please provide here the contact's first name
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="lastname">last name</label>
                        <input type="text" id="lastname" class="form-control" name="lastname" aria-describedby="lastnameHelp" />
                        <small id="lastnameHelp" class="form-text text-muted">
                            Please provide here the contact's last name
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="streetNo">street and number</label>
                        <input type="text" id="streetNo" class="form-control" name="streetNo" aria-describedby="streetNoHelp" />
                        <small id="streetNoHelp" class="form-text text-muted">
                            Please provide here the contact's street and number
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="zip">zip</label>
                        <input type="text" id="zip" class="form-control" name="zip" aria-describedby="zipHelp" />
                        <small id="zipHelp" class="form-text text-muted">
                            Please provide here the contact's zip code
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="city">city</label>
                        <input type="text" id="city" class="form-control" name="city" aria-describedby="cityHelp" />
                        <small id="cityHelp" class="form-text text-muted">
                            Please provide here the contact's city name
                        </small>
                    </div>
                    <div class="form-group" v-if="countries.length > 0">
                        <label for="country">country</label>
                        <select id="country" name="country" class="form-control" aria-describedby="countryHelp">
                            <option v-for="country in countries" :value="country.isoAlpha2">
                                {{country.name}}
                            </option>
                        </select>
                        <small id="countryHelp" class="form-text text-muted">
                            Please provide the country in which the contact lives
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="phone">phone number</label>
                        <input type="tel" id="phone" class="form-control" name="phone" aria-describedby="phoneHelp" required="required"/>
                        <small id="phoneHelp" class="form-text text-muted">
                            Please provide here the contact's first name
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="birthday">Birthday</label>
                        <input type="date" id="birthday" class="form-control" name="birthday" aria-describedby="birthdayHelp" />
                        <small id="birthdayHelp" class="form-text text-muted">
                            Please provide here the contact's first name
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="email">email address</label>
                        <input type="email" id="email" class="form-control" name="email" aria-describedby="emailHelp" />
                        <small id="emailHelp" class="form-text text-muted">
                            Please provide here the contact's email address
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="pictureUrl">picture</label>
                        <input type="file" id="pictureUrl" class="form-control" name="pictureUrl" aria-describedby="pictureUrlHelp" />
                        <small id="pictureUrlHelp" class="form-text text-muted">
                            Please provide here the contact's picture, if any
                        </small>
                    </div>
                    <input type="hidden" name="token" v-bind:value="token" />
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col" v-if="contacts.length > 0">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">id</th>
                            <th scope="col">firstname</th>
                            <th scope="col">lastname</th>
                            <th scope="col">street and number</th>
                            <th scope="col">zip</th>
                            <th scope="col">city</th>
                            <th scope="col">country</th>
                            <th scope="col">phone</th>
                            <th scope="col">birthday</th>
                            <th scope="col">email</th>
                            <th scope="col">picture</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="contact in contacts">
                            <td>{{contact.id}}</td>
                            <td>{{contact.firstname}}</td>
                            <td>{{contact.lastname}}</td>
                            <td>{{contact.streetNo}}</td>
                            <td>{{contact.zip}}</td>
                            <td>{{contact.city}}</td>
                            <td>{{contact.country}}</td>
                            <td><a :href="'tel:' + contact.phone" rel="noopener">{{contact.phone}}</a></td>
                            <td>{{contact.birthday}}</td>
                            <td><a :href="'mailto:' + contact.email" rel="noopener">{{contact.email}}</a></td>
                            <td>
                                <span v-if="contact.pictureUrl">
                                    <img :src="contact.pictureUrl" :alt="contact.firstname + ' ' + contact.lastname + '\'s picture'">
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <nav aria-label="Page navigation" v-if="pageCount > 0">
                    <ul class="pagination">
                        <li class="page-item">
                            <a class="page-link" :disabled="page <= 1" :href="'/overview/getList?page=' + (page - 1)" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
                        <li class="page-item" v-for="pIt in pageCount">
                            <a :href="'/overview/getList?page=' + pIt">{{pIt}}</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" :disabled="page >= pageCount" :href="'/overview/getList?page=' + (page + 1)" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    // const axios = require('axios').default;

    import {Component, Prop, Vue} from 'vue-property-decorator';
    import HelloWorld from './components/HelloWorld.vue';
    import axios from 'axios';

    @Component({
        components: {
            HelloWorld,
        },
    })
    export default class App extends Vue
    {
        // das ! ist noetig fuer ist einfach so da ohne Konstruktor
        public countries: {isoAlpha2: string, name: string}[] = [];
        public contacts: {
            id: number,
            firstname: string,
            lastname: string,
            streetNo: string,
            zip: string,
            city: string,
            countryIsoAlpha2: string,
            country: string,
            phone: string,
            birthday: string,
            email: string,
            pictureUrl: string|null
        }[] = [];

        public pageCount: number = 0;

        public page: number = 1;

        public token: string|null;

        constructor()
        {
            super();
            this.token = (<HTMLInputElement>document.getElementById('get-token')).value;
            console.log('this.token', this.token);
            this.fillCountryList();
            this.renderContactList();
            this.renderPagination();
        }

        fillCountryList(): void
        {
            let me = this;
            axios.get('/overview/getCountries').then(
                response =>
                {
                    this.countries = response.data;
                }
            );
        }

        onNewAddressSubmitted(eventArgs: any): void
        {
            let frmData = new FormData(eventArgs.target as HTMLFormElement);
            axios.post(
                '/overview/saveContact',
                frmData,
                {
                    'headers': {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            ).then((result) =>
                {
                    this.onSaveContactSuccess(result);
                }
            ).catch((error) =>
                {
                    this.onSaveContactError(error);
                }
            );
        }

        onSaveContactSuccess(result: any): void
        {
            console.log('xhr post success', result);
            this.renderContactList();
            this.renderPagination();
        }

        onSaveContactError(error: any): void
        {
            console.warn('xhr post error', error);
        }

        renderContactList(): void
        {
            let me = this;

            axios.get('/overview/getList').then(
                response =>
                {
                    this.contacts = response.data;
                    this.doRenderContactList();
                }
            );
        }

        doRenderContactList(): void
        {

        }

        renderPagination(): void
        {
            let me = this;

            axios.get('/overview/getPageCount').then(
                response =>
                {
                    this.pageCount = response.data;
                    this.doRenderPagination();
                }
            );
        }

        doRenderPagination(): void
        {

        }
    }
</script>

<style>
    #app {
        font-family: Avenir, Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: #2c3e50;
        margin-top: 60px;
    }
</style>
