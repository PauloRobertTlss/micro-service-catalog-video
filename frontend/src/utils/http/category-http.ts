import {httpVideo} from "./index";
import HttpResource from "./http-resource";


const categoryHttp = new HttpResource(httpVideo, "categories");

export default categoryHttp;