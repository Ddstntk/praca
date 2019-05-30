import { Component, OnInit } from '@angular/core';
import {Router, ActivatedRoute, Params} from '@angular/router';
import {RegisterComponentService} from "./register.component.service";


@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss'],
  // providers: [UsersComponentService]
})
export class RegisterComponent implements OnInit {
  yearList: any;
  name: string;
  surname: string;
  email: string;
  password: string;
  description: string;
  birthDate: any;
  imageId: any;
  avatarList = [];
  userValid = false;

  constructor(private registerService: RegisterComponentService, private route: ActivatedRoute, private router: Router) { }

  ngOnInit() {
    this.yearList = [];
    for(let i=new Date().getFullYear(); i > 1940; i--){
      this.yearList.push(i);
    }
    for(let i=1; i <51; i++){
      this.avatarList.push(i);
    }
  }
  validate() {
    if(this.name && this.surname && this.email && this.password && this.birthDate && this.imageId){
      this.userValid = true;
    } else {
      this.userValid = false;
    }
  }
  setImage(id) {
    this.imageId = id;
  }
  addUser(){
    alert(JSON.stringify(this.birthDate))
    var user = {
      name: this.name,
      surname: this.surname,
      email: this.email,
      password: this.password,
      description: this.description,
      birthDate: this.birthDate,
      imageId: this.imageId
    }
      this.registerService.addUser(user).subscribe(data=>{

      })
  }
}
