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
                        <label for="tfFirstname">first name</label>
                        <input type="text" id="tfFirstname" class="form-control" name="tfFirstname" aria-describedby="tfFirstnameHelp" />
                        <small id="tfFirstnameHelp" class="form-text text-muted">
                            Please provide here the contact's first name
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="tfLastname">last name</label>
                        <input type="text" id="tfLastname" class="form-control" name="tfLastname" aria-describedby="tfLastnameHelp" />
                        <small id="tfLastnameHelp" class="form-text text-muted">
                            Please provide here the contact's last name
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="tfStreetNo">street and number</label>
                        <input type="text" id="tfStreetNo" class="form-control" name="tfStreetNo" aria-describedby="tfStreetNoHelp" />
                        <small id="tfStreetNoHelp" class="form-text text-muted">
                            Please provide here the contact's street and number
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="tfZip">zip</label>
                        <input type="text" id="tfZip" class="form-control" name="tfZip" aria-describedby="tfZipHelp" />
                        <small id="tfZipHelp" class="form-text text-muted">
                            Please provide here the contact's zip code
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="tfCity">city</label>
                        <input type="text" id="tfCity" class="form-control" name="tfCity" aria-describedby="tfCityHelp" />
                        <small id="tfCityHelp" class="form-text text-muted">
                            Please provide here the contact's city name
                        </small>
                    </div>
                    <div class="form-group" v-if="countries.length > 0">
                        <label for="pdCountry">country</label>
                        <select id="pdCountry" name="pdCountry" class="form-control" aria-describedby="pdCountryHelp">
                            <option v-for="country in countries" :value="country.isoAlpha2">
                                {{country.name}}
                            </option>
                        </select>
                        <small id="pdCountryHelp" class="form-text text-muted">
                            Please provide the country in which the contact lives
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="tfPhone">phone number</label>
                        <input type="tel" id="tfPhone" class="form-control" name="tfPhone" aria-describedby="tfPhoneHelp" required="required"/>
                        <small id="tfPhoneHelp" class="form-text text-muted">
                            Please provide here the contact's first name
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="tfBirthday">Birthday</label>
                        <input type="date" id="tfBirthday" class="form-control" name="tfBirthday" aria-describedby="tfBirthdayHelp" />
                        <small id="tfBirthdayHelp" class="form-text text-muted">
                            Please provide here the contact's first name
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="tfEmail">email address</label>
                        <input type="text" id="tfEmail" class="form-control" name="tfEmail" aria-describedby="tfEmailHelp" />
                        <small id="tfEmailHelp" class="form-text text-muted">
                            Please provide here the contact's email address
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="tfPicture">picture</label>
                        <input type="file" id="tfPicture" class="form-control" name="tfPicture" aria-describedby="tfPictureHelp" />
                        <small id="tfPictureHelp" class="form-text text-muted">
                            Please provide here the contact's picture, if any
                        </small>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
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

        constructor()
        {
            super();
            this.fillCountryList();
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
        }

        onSaveContactError(error: any): void
        {
            console.warn('xhr post error', error);
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
