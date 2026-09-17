export interface Profile {
  username: string;
  bio: string | null;
  image: string | null;
  following: boolean;
  skills: string[];   // <--- Se agregó ESTA LÍNEA
  languages: string[]; // <--- Se agregó ESTA LÍNEA
}
