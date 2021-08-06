export interface AjaxErrorRepsonse {
    type: string,
    title: string,
    errors: string[],
}

export interface MapStringTo<T> { [key:string]:T; }
